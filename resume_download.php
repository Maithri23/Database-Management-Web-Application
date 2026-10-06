<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['resume_content'])) {
    $resume_content = htmlspecialchars_decode($_POST['resume_content']);
    $paragraphs = explode("\n", $resume_content);
    $body = '';

    foreach ($paragraphs as $para) {
        $escaped = htmlspecialchars(trim($para));
        if (empty($escaped)) continue;

        // Bullet points
        if (strpos($escaped, '•') === 0 || strpos($escaped, '-') === 0) {
            $body .= '<w:p><w:r><w:t xml:space="preserve">' . $escaped . '</w:t></w:r></w:p>';
        }
        // Bold known section headers
        elseif (preg_match('/^(Name|Email|Phone|Contact|LinkedIn|Portfolio|Professional Summary|Work Experience|Experience|Skills|Projects|Education|Awards|Certifications|Languages|Objective|Summary|Volunteer Experience|Affiliations|Publications)(:)?$/i', $escaped)) {
            $body .= <<<EOT
<w:p>
  <w:r>
    <w:rPr><w:b/></w:rPr>
    <w:t xml:space="preserve">{$escaped}</w:t>
  </w:r>
</w:p>
EOT;
        }
        // Regular text
        else {
            $body .= '<w:p><w:r><w:t xml:space="preserve">' . $escaped . '</w:t></w:r></w:p>';
        }
    }

    $document_xml = <<<XML
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">
  <w:body>
    {$body}
  </w:body>
</w:document>
XML;

    $zip = new ZipArchive();
    $tmp_file = tempnam(sys_get_temp_dir(), 'docx');

    if ($zip->open($tmp_file, ZipArchive::CREATE) !== TRUE) {
        die("Unable to open file");
    }

    $zip->addEmptyDir('word');
    $zip->addEmptyDir('_rels');
    $zip->addEmptyDir('docProps');
    $zip->addEmptyDir('word/_rels');

    $zip->addFromString('[Content_Types].xml', <<<XML
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
  <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
  <Default Extension="xml" ContentType="application/xml"/>
  <Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/>
  <Override PartName="/docProps/core.xml" ContentType="application/vnd.openxmlformats-package.core-properties+xml"/>
  <Override PartName="/docProps/app.xml" ContentType="application/vnd.openxmlformats-officedocument.extended-properties+xml"/>
</Types>
XML);

    $zip->addFromString('_rels/.rels', <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="word/document.xml"/>
</Relationships>
XML);

    $zip->addFromString('word/_rels/document.xml.rels', <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"></Relationships>
XML);

    $zip->addFromString('docProps/core.xml', <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<cp:coreProperties xmlns:cp="http://schemas.openxmlformats.org/package/2006/metadata/core-properties"
                   xmlns:dc="http://purl.org/dc/elements/1.1/"
                   xmlns:dcterms="http://purl.org/dc/terms/"
                   xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
  <dc:title>ATS Resume</dc:title>
  <dc:creator>ATS Generator</dc:creator>
</cp:coreProperties>
XML);

    $zip->addFromString('docProps/app.xml', <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<Properties xmlns="http://schemas.openxmlformats.org/officeDocument/2006/extended-properties">
  <Application>PHP ATS Generator</Application>
</Properties>
XML);

    $zip->addFromString('word/document.xml', $document_xml);
    $zip->close();

    header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
    header('Content-Disposition: attachment; filename="ATS_Resume.docx"');
    readfile($tmp_file);
    unlink($tmp_file);
    exit();
}
echo "Invalid request.";
