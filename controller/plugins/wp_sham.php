<?php

function wp_next_scheduled($hook, $args = array()) {
    $next_event = wp_get_scheduled_event($hook, $args);
    if (!$next_event) {
        return false;
    }

    return $next_event->timestamp;
}

function wp_unschedule_event($timestamp, $hook, $args = array(), $wp_error = false) {
    // Make sure timestamp is a positive integer.
    if (!is_numeric($timestamp) || $timestamp <= 0) {
        if ($wp_error) {
            return new WP_Error(
                    'invalid_timestamp',
                    __('Event timestamp must be a valid Unix timestamp.')
            );
        }

        return false;
    }

    /**
     * Filter to preflight or hijack unscheduling of events.
     *
     * Returning a non-null value will short-circuit the normal unscheduling
     * process, causing the function to return the filtered value instead.
     *
     * For plugins replacing wp-cron, return true if the event was successfully
     * unscheduled, false or a WP_Error if not.
     *
     * @since 5.1.0
     * @since 5.7.0 The `$wp_error` parameter was added, and a `WP_Error` object can now be returned.
     *
     * @param null|bool|WP_Error $pre       Value to return instead. Default null to continue unscheduling the event.
     * @param int                $timestamp Timestamp for when to run the event.
     * @param string             $hook      Action hook, the execution of which will be unscheduled.
     * @param array              $args      Arguments to pass to the hook's callback function.
     * @param bool               $wp_error  Whether to return a WP_Error on failure.
     */
    $pre = apply_filters('pre_unschedule_event', null, $timestamp, $hook, $args, $wp_error);

    if (null !== $pre) {
        if ($wp_error && false === $pre) {
            return new WP_Error(
                    'pre_unschedule_event_false',
                    __('A plugin prevented the event from being unscheduled.')
            );
        }

        if (!$wp_error && is_wp_error($pre)) {
            return false;
        }

        return $pre;
    }

    $crons = _get_cron_array();
    $key = md5(serialize($args));
    unset($crons[$timestamp][$hook][$key]);
    if (empty($crons[$timestamp][$hook])) {
        unset($crons[$timestamp][$hook]);
    }
    if (empty($crons[$timestamp])) {
        unset($crons[$timestamp]);
    }

    return _set_cron_array($crons, $wp_error);
}

function wp_schedule_event($timestamp, $recurrence, $hook, $args = array(), $wp_error = false) {
    // Make sure timestamp is a positive integer.
    if (!is_numeric($timestamp) || $timestamp <= 0) {
        if ($wp_error) {
            return new WP_Error(
                    'invalid_timestamp',
                    __('Event timestamp must be a valid Unix timestamp.')
            );
        }

        return false;
    }

    $schedules = wp_get_schedules();

    if (!isset($schedules[$recurrence])) {
        if ($wp_error) {
            return new WP_Error(
                    'invalid_schedule',
                    __('Event schedule does not exist.')
            );
        }

        return false;
    }

    $event = (object) array(
                'hook' => $hook,
                'timestamp' => $timestamp,
                'schedule' => $recurrence,
                'args' => $args,
                'interval' => $schedules[$recurrence]['interval'],
    );

    /** This filter is documented in wp-includes/cron.php */
    $pre = apply_filters('pre_schedule_event', null, $event, $wp_error);

    if (null !== $pre) {
        if ($wp_error && false === $pre) {
            return new WP_Error(
                    'pre_schedule_event_false',
                    __('A plugin prevented the event from being scheduled.')
            );
        }

        if (!$wp_error && is_wp_error($pre)) {
            return false;
        }

        return $pre;
    }

    /** This filter is documented in wp-includes/cron.php */
    $event = apply_filters('schedule_event', $event);

    // A plugin disallowed this event.
    if (!$event) {
        if ($wp_error) {
            return new WP_Error(
                    'schedule_event_false',
                    __('A plugin disallowed this event.')
            );
        }

        return false;
    }

    $key = md5(serialize($event->args));

    $crons = _get_cron_array();

    $crons[$event->timestamp][$event->hook][$key] = array(
        'schedule' => $event->schedule,
        'args' => $event->args,
        'interval' => $event->interval,
    );
    uksort($crons, 'strnatcasecmp');

    return _set_cron_array($crons, $wp_error);
}

function is_singular($post_types = '') {
    global $wp_query;

    if (!isset($wp_query)) {
        _doing_it_wrong(__FUNCTION__, __('Conditional query tags do not work before the query is run. Before then, they always return false.'), '3.1.0');
        return false;
    }

    return $wp_query->is_singular($post_types);
}

function get_the_ID() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
    $post = get_post();
    return !empty($post) ? $post->ID : false;
}

function wp_reset_postdata() {
    global $wp_query;

    if (isset($wp_query)) {
        $wp_query->reset_postdata();
    }
}

function is_admin() {
    if (isset($GLOBALS['current_screen'])) {
        return $GLOBALS['current_screen']->in_admin();
    } elseif (defined('WP_ADMIN')) {
        return WP_ADMIN;
    }

    return false;
}

/**
 * Hooks a function on to a specific action.
 *
 * Actions are the hooks that the WordPress core launches at specific points
 * during execution, or when specific events occur. Plugins can specify that
 * one or more of its PHP functions are executed at these points, using the
 * Action API.
 *
 * @uses add_filter() Adds an action. Parameter list and functionality are the same.
 *
 * @since 1.2.0
 *
 * @param string $tag The name of the action to which the $function_to_add is hooked.
 * @param callback $function_to_add The name of the function you wish to be called.
 * @param int $priority optional. Used to specify the order in which the functions associated with a particular action are executed (default: 10). Lower numbers correspond with earlier execution, and functions with the same priority are executed in the order in which they were added to the action.
 * @param int $accepted_args optional. The number of arguments the function accept (default 1).
 */
function add_action($tag, $function_to_add, $priority = 10, $accepted_args = 1) {
    return add_filter($tag, $function_to_add, $priority, $accepted_args);
}

/**
 * Set the deactivation hook for a plugin.
 *
 * When a plugin is deactivated, the action 'deactivate_PLUGINNAME' hook is
 * called. In the name of this hook, PLUGINNAME is replaced with the name
 * of the plugin, including the optional subdirectory. For example, when the
 * plugin is located in wp-content/plugins/sampleplugin/sample.php, then
 * the name of this hook will become 'deactivate_sampleplugin/sample.php'.
 *
 * When the plugin consists of only one file and is (as by default) located at
 * wp-content/plugins/sample.php the name of this hook will be
 * 'deactivate_sample.php'.
 *
 * @since 2.0.0
 *
 * @param string $file The filename of the plugin including the path.
 * @param callback $function the function hooked to the 'deactivate_PLUGIN' action.
 */
function register_deactivation_hook($file, $function) {
    $file = plugin_basename($file);
    add_action('deactivate_' . $file, $function);
}

function wp_insert_post( array $postarr, bool $wp_error = false, bool $fire_after_hooks = true ): int|WP_Error {
    
}