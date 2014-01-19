<?php
    /**
     * Convert HTML to MS Word file
     * @author Harish Chauhan
     * @version 1.0.0
     * @name HTML_TO_DOC
     */

    class HTML_TO_DOC
    {
        var $docFile="";
        var $title="";
        var $htmlHead="";
        var $htmlBody="";
        var $CI;


        /**
         * Constructor
         *
         * @return void
         */
        function HTML_TO_DOC()
        {
            $this->title="Untitled Document";
            $this->htmlHead="";
            $this->htmlBody="";
        }

        /**
         * Set the document file name
         *
         * @param String $docfile
         */

        function setDocFileName($docfile)
        {
            $this->docFile=$docfile;
            if(!preg_match("/\.doc$/i",$this->docFile))
                $this->docFile.=".doc";
            return;
        }

        function setTitle($title)
        {
            $this->title=$title;
        }

        /**
         * Return header of MS Doc
         *
         * @return String
         */
        function getHeader()
        {
            $return  = <<<EOH
             <html xmlns:v="urn:schemas-microsoft-com:vml"
            xmlns:o="urn:schemas-microsoft-com:office:office"
            xmlns:w="urn:schemas-microsoft-com:office:word"
            xmlns="http://www.w3.org/TR/REC-html40">

            <head>
            <meta http-equiv=Content-Type content="text/html; charset=utf-8">
            <meta name=ProgId content=Word.Document>
            <meta name=Generator content="Microsoft Word 9">
            <meta name=Originator content="Microsoft Word 9">
            <!--[if !mso]>
            <style>
            v\:* {behavior:url(#default#VML);}
            o\:* {behavior:url(#default#VML);}
            w\:* {behavior:url(#default#VML);}
            .shape {behavior:url(#default#VML);}
            </style>
            <![endif]-->
            <title>$this->title</title>
            <!--[if gte mso 9]>
            <xml>
            <w:WordDocument>
            <w:View>Print</w:View>
            <w:Zoom>90</w:Zoom>
            <w:DoNotOptimizeForBrowser/>
            </w:WordDocument>
            </xml> <![endif]-->
            <style>
            <!--
             /* Font Definitions */
             @font-face
                {font-family:Wingdings;
                panose-1:5 0 0 0 0 0 0 0 0 0;
                mso-font-charset:2;
                mso-generic-font-family:auto;
                mso-font-pitch:variable;
                mso-font-signature:0 268435456 0 0 -2147483648 0;}
            @font-face
                {font-family:Tahoma;
                panose-1:2 11 6 4 3 5 4 4 2 4;
                mso-font-charset:0;
                mso-generic-font-family:swiss;
                mso-font-pitch:variable;
                mso-font-signature:1627421319 -2147483648 8 0 66047 0;}
            @font-face
                {font-family:"Trebuchet MS";
                panose-1:2 11 6 3 2 2 2 2 2 4;
                mso-font-charset:0;
                mso-generic-font-family:swiss;
                mso-font-pitch:variable;
                mso-font-signature:647 0 0 0 159 0;}
            @font-face
                {font-family:Cambria;
                panose-1:2 4 5 3 5 4 6 3 2 4;
                mso-font-charset:0;
                mso-generic-font-family:roman;
                mso-font-pitch:variable;
                mso-font-signature:-1610611985 1073741899 0 0 159 0;}
            @font-face
                {font-family:Calibri;
                panose-1:2 15 5 2 2 2 4 3 2 4;
                mso-font-charset:0;
                mso-generic-font-family:swiss;
                mso-font-pitch:variable;
                mso-font-signature:-1610611985 1073750139 0 0 159 0;}
             /* Style Definitions */
             p.MsoNormal, li.MsoNormal, div.MsoNormal
                {mso-style-parent:"";
                margin-top:0in;
                margin-right:0in;
                margin-bottom:10.0pt;
                margin-left:0in;
                line-height:115%;
                mso-pagination:widow-orphan;
                font-size:12.0pt;
                font-family:Calibri;
                mso-fareast-font-family:Calibri;
                mso-bidi-font-family:"Times New Roman";}
            @page Section1
                {size:8.5in 11.0in;
                margin:1.0in .5in 1.0in .5in;
                mso-header-margin:.5in;
                mso-footer-margin:.5in;
                mso-paper-source:0;}
            div.Section1
                {page:Section1;}
             /* List Definitions */
            table
                {border:0px; border-color: white; font-size:12px;}
                #wrapper {

                }

                .title, #closewindow, #printbutton {
                display:none;
                }

                #quotefooter {
                clear:both;
                padding:10px;
                border:1px solid #000;
                }

                #logo, #left-details,
                #salesrep, #companyinfo,
                #addetails,#signature {
                border:1px solid #000;
                padding:0 10px;
                margin-bottom:10px;
                }

                #left-details {
                padding:10px 35px;
                }

                #agreement {
                font-size:10px;
                }

                h2 {
                font-size:14px;
                }

                h3 {
                font-size: 12px;
                }

                #left-details .hr{
                height:8px;
                background-color:#622423;
                }

                #logo {
                padding:10px 0px;
                text-align:center;
                }

                #name {
                width:200px;
                }

                #infoforad {
                text-align:center;
                min-height:450px;
                }

                .name{
                float:left;
                width:300px;
                margin-right:10px;
                }

                #namefield {
                float:right;
                width:85%;
                height:20px;
                border-bottom:1px solid #000;
                }

                #date {
                width:170px;
                float:left;
                }

                #datefield {
                float:right;
                height:20px;
                width:70%;
                border-bottom:1px solid #000;
                }

                #signature sub {
                display:block;
                clear:both;
                font-size:12px;
                margin:20px 0 0 55px;
                }
            -->
            </style>
            <!--[if gte mso 9]><xml>
             <o:shapedefaults v:ext="edit" spidmax="1032">
              <o:colormenu v:ext="edit" strokecolor="none"/>
             </o:shapedefaults></xml><![endif]--><!--[if gte mso 9]><xml>
             <o:shapelayout v:ext="edit">
              <o:idmap v:ext="edit" data="1"/>
             </o:shapelayout></xml><![endif]-->
             $this->htmlHead
            </head>
            <body>
EOH;
        return $return;
        }

        /**
         * Return Document footer
         *
         * @return String
         */
        function getFotter()
        {
            return "</body></html>";
        }

        /**
         * Create The MS Word Document from given HTML
         *
         * @param String $html :: URL Name like http://www.example.com
         * @param String $file :: Document File Name
         * @param Boolean $download :: Wheather to download the file or save the file
         * @return boolean
         */

        function createDocFromURL($url,$file,$download=false)
        {
            $this->CI =& get_instance();
            if(!preg_match("/^http:/",$url))
                $url="http://".$url;
            //$html=@file_get_contents($url);
            $html = $this->CI->searchmodel->loadfile($url);
            return $this->createDoc($html,$file,$download);
        }

        /**
         * Create The MS Word Document from given HTML
         *
         * @param String $html :: HTML Content or HTML File Name like path/to/html/file.html
         * @param String $file :: Document File Name
         * @param Boolean $download :: Wheather to download the file or save the file
         * @return boolean
         */

        function createDoc($html,$file,$download=false)
        {
            if(is_file($html))
                $html=@file_get_contents($html);

            $this->_parseHtml($html);
            $this->setDocFileName($file);
            $doc=$this->getHeader();
            $doc.=$this->htmlBody;
            $doc.=$this->getFotter();

            if($download)
            {
                @header("Cache-Control: ");// leave blank to avoid IE errors
                @header("Pragma: ");// leave blank to avoid IE errors
                @header("Content-type: application/octet-stream");
                @header("Content-Disposition: attachment; filename=\"$this->docFile\"");
                echo $doc;
                return true;
            }
            else
            {
                die();
                return $this->write_file($this->docFile,$doc);
            }
        }

        /**
         * Parse the html and remove <head></head> part if present into html
         *
         * @param String $html
         * @return void
         * @access Private
         */

        function _parseHtml($html)
        {
            $html=preg_replace("/<!DOCTYPE((.|\n)*?)>/ims","",$html);
            $html=preg_replace("/<script((.|\n)*?)>((.|\n)*?)<\/script>/ims","",$html);
            preg_match("/<head>((.|\n)*?)<\/head>/ims",$html,$matches);
            $head=isset($matches[1]) ? $matches[1] : NULL;
            preg_match("/<title>((.|\n)*?)<\/title>/ims",$head,$matches);
            $this->title = isset($matches[1]) ? $matches[1] : NULL;
            $html=preg_replace("/<head>((.|\n)*?)<\/head>/ims","",$html);
            $head=preg_replace("/<title>((.|\n)*?)<\/title>/ims","",$head);
            $head=preg_replace("/<\/?head>/ims","",$head);
            $html=preg_replace("/<\/?body((.|\n)*?)>/ims","",$html);
            $this->htmlHead=$head;
            $this->htmlBody=$html;
            return;
        }

        /**
         * Write the content int file
         *
         * @param String $file :: File name to be save
         * @param String $content :: Content to be write
         * @param [Optional] String $mode :: Write Mode
         * @return void
         * @access boolean True on success else false
         */

        function write_file($file,$content,$mode="w")
        {
            $fp=@fopen($file,$mode);
            if(!is_resource($fp))
                return false;
            fwrite($fp,$content);
            fclose($fp);
            return true;
        }

    }