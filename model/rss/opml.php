<?php
/*
OPML (Outline Processor Markup Language) is an XML format for outlines (defined as "a tree, where each node contains a set of named attributes with string values"[1]). Originally developed by UserLand as a native file format for the outliner application in its Radio UserLand product, it has since been adopted for other uses, the most common being to exchange lists of web feeds between web feed aggregators.

The OPML specification defines an outline as a hierarchical, ordered list of arbitrary elements. The specification is fairly open which makes it suitable for many types of list data.

Support for importing and exporting RSS feed lists in OPML format is available in Mozilla Thunderbird,[2] and many other RSS reader web sites and applications.

*/
ini_set('display_errors', 'On');
error_reporting(E_ALL);

class opml
{
    private $data;
    private $writer;

    public function __construct(array $data)
    {
        $this->data = $data;
        $this->writer = new XMLWriter();
        $this->writer->openMemory();
    }

    public function opmlRender()
    {
        $this->writer->startDocument('1.0', 'UTF-8');
        $this->writer->startElement('opml');
        $this->writer->writeAttribute('version', '2.0');

        // Header
        $this->writer->startElement('head');
        foreach ($this->data['head'] as $key => $value) {
            $this->writer->writeElement($key, $value);
        }
        $this->writer->writeElement('dateModified', date("D, d M Y H:i:s T"));
        $this->writer->endElement();

        // Body
        $this->writer->startElement('body');
        foreach ($this->data['body'] as $outlines) {
            $this->writer->startElement('outline');
            foreach ($outlines as $key => $value) {
                $this->writer->writeAttribute($key, $value);
            }
            $this->writer->endElement();
        }
        $this->writer->endElement();

        $this->writer->endElement();
        $this->writer->endDocument();
        return $this->writer->outputMemory();
    }
}
