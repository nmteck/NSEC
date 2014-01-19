<?php

    function jFlowScript(){
        $CI =& get_instance();
        $CI->load->library('user_agent');

        if($CI->agent->browser() == 'Internet Explorer' && $CI->agent->version() != 8)
        {
            return '
            <style>
                .jFlowNext, .jFlowPrev, #myController {display:none; clear:both;}
            </style>';
        } else {
            return <<<SCRIPT
            <script language="javascript" type="text/javascript"><!--
            $(document).ready(function(){
                $("#myController").jFlow({
                    slides: "#mySlides",
                    controller: ".jFlowControl", // must be class, use . sign
                    slideWrapper : "#jFlowSlide", // must be id, use # sign
                    selectedWrapper: "jFlowSelected",  // just pure text, no sign
                    duration: 1000,
                    prev: ".jFlowPrev", // must be class, use . sign
                    next: ".jFlowNext" // must be class, use . sign
                });
            });
            // --></script>
SCRIPT;
        }
    }

    function paginate($current, $total, $perpage, $link, $brake = 25)
    {
        if ($total<=$perpage)
        {
            return '';
        }

        $br = "<br style='clear: both'>";
        $return = $br;

        $pages = floor(($total - 1 ) / $perpage) + 1;

        $half = floor($brake/2);
        $start = $current>$half ? $current-$half : 1;
        $end = $current<$pages-$half-1 ? $current+$half+1 : $pages;

        for ($j=$start; $j<=$end; $j++)
        {
            $return .= "<a".($j == $current ? " style='font-weight: bold'" : '')
                ." href='/$link".($j ? "/".$j : '')
                ."' class='page'>".$j."</a>";
        }

        return "<div align='center'>$return$br$br</div>";
    }

    function appendToPageLevelMessage($message, $type = 'error')
    {
        $CI =& get_instance();
        if ($type === 'success') {
            $CI->form_validation->error_string .= "<div class='success'>$message</div>";
        } else {
            $CI->form_validation->error_string .= "<div class='error'>$message</div>";
        }
    }