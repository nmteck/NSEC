<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ajax extends CI_Controller {

    public $pageType;

    function __construct()
    {
        parent::__construct();
        $this->load->helper('nsec_account');
    }

    private function _jsonHeader(){
          header('Cache-Control: no-cache, must-revalidate');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        //header('Content-type: application/json');
        header('Content-type: text/html');
    }

    function getMenuItems()
    {
        jsonHeader();
        $where = $output = NULL;
        $idList = getUserAccountInfo('AccessLevels');

        $results = $this->db->query(
            "select * from access_types where AccessLevelID in ("
            . (($idList !== null) ? $idList : 1)
            . ") order by ParentId, AccessRef"
        );

          if ($results!=false && $results->num_rows() > 0) {
              foreach ( $results->result() as $row ) {
                    $output[$row->AccessLevelID] = array(
                      'value' => $row->AccessPage,
                      'text' => $row->AccessName,
                      'parent' => $row->ParentId,
                      'ref' => $row->AccessRef
                    );
              }
          }

          $output['1000'] = array('value' => '/account/logout', 'text' => 'Logout', 'parent' => null);

          echo json_encode($output);
          die();
    }

    function weather()
    {
        jsonHeader();
        $where = $output = NULL;

        $config['hostname'] = "nmteck.info";
        $config['username'] = "root";
        $config['password'] = "3n3rgy";
        $config['database'] = "nmtdev_weather";
        $config['dbdriver'] = "mysql";
        $config['dbprefix'] = "";
        $config['pconnect'] = FALSE;
        $config['db_debug'] = TRUE;
        $config['cache_on'] = FALSE;
        $config['cachedir'] = "";
        $config['char_set'] = "utf8";
        $config['dbcollat'] = "utf8_general_ci";

        $DB =& $this->load->database($config, TRUE);

        $DB->order_by('epoch', 'asc');
        $forecasts = $DB->get('forecast_data');

        foreach ($forecasts->result() as $forecast) {
            $output['forecast'][] = array(
                'image' => 'cloudy.png',
                'temp' => intval($forecast->highTemp),
                'temp2' => intval($forecast->highTemp),
                'highTemp' => intval($forecast->highTemp),
                'lowTemp' => intval($forecast->lowTemp),
                'date' => date('M j', strtotime($forecast->date)),
                'summary' => $forecast->summary,
                'title' => $forecast->title,
                'rain' => intval($forecast->rain),
                'wind' => array('speed' => intval($forecast->windSpeed), 'direction' => $forecast->windDirection),
                'sunrise' => "6:54am",
                'sunset' => "7:33pm",
                'humidity' => intval($forecast->humidity)
            );
        }


        $output['marine'][] = array(
            'image' => 'cloudy.png',
            'temp' => 87,
            'highTemp' => 89,
            'lowTemp' => 85,
            'date' => 'Jun 6',
            'title' => 'Partly Cloudy',
            'rain' => 20,
            'wind' => array('speed' => 2, 'direction' => 'N'),
            'sunrise' => "6:54am",
            'sunset' => "7:33pm",
            'humiidity' => 88
        );

        $output['marine'][] = array(
            'image' => 'fog.png',
            'temp' => 87,
            'highTemp' => 89,
            'lowTemp' => 85,
            'date' => 'Jun 7',
            'title' => 'Foggy',
            'rain' => 20,
            'wind' => array('speed' => 6, 'direction' => 'N/NE'),
            'sunrise' => "6:54am",
            'sunset' => "7:33pm",
            'humiidity' => 88
        );

        $output['alerts'][] = array(
            'image' => 'sunny.png',
            'temp' => 87,
            'highTemp' => 89,
            'lowTemp' => 85,
            'summary' => "This is a test summary, this is a test summary, this is a test summaryThis is a test summary, this is a test summary, this is a test summaryThis is a test summary, this is a test summary, this is a test summaryThis is a test summary, this is a this is a test summary, this is a test summaryThis is a test summary, this is a test summary, this is a test summaryThis is a test summary, this is a test summary, this is a test summary",
            'date' => 'Jun 5',
            'title' => 'Rain Showers',
            'rain' => 20,
            'wind' => array('speed' => 4, 'direction' => 'N/NE'),
            'sunrise' => "6:54am",
            'sunset' => "7:33pm",
            'humiidity' => 88
        );

        $output['alerts'][] = array(
            'image' => 'sunny.png',
            'temp' => 87,
            'highTemp' => 89,
            'lowTemp' => 85,
            'summary' => "t summary, this is a test summaryThis is a test summary, this is a test summary, this is a test summaryThis is a test",
            'date' => 'Jun 5',
            'title' => 'Rain Showers',
            'rain' => 20,
            'wind' => array('speed' => 4, 'direction' => 'N/NE'),
            'sunrise' => "6:54am",
            'sunset' => "7:33pm",
            'humiidity' => 88
        );

        $output['weatherMapUrl'] = "http://api.wunderground.com/api/dbe37e8ec02b29dc/animatedradar/animatedsatellite/q/BS/Nassau.gif?rad.smooth=1&sat.radius=200&num=15&delay=10&interval=30&sat.width=550&sat.height=550&&rad.width=550&rad.height=550&sat.timelabel=1";
        $output['updated'] = date('Y-m-d H:i:s');

        echo json_encode($output);
        die();
    }

    private function import_weather() {
        $data = $this->CallAPI('GET', 'http://api.wunderground.com/api/dbe37e8ec02b29dc/forecast10day/q/BS/Nassau.json');
        $forecasts = array();

        $data = json_decode($data);
        foreach ($data->forecast->txt_forecast->forecastday as $forecast) {
            if (isset($forecasts[$forecast->title]) || strstr($forecast->fcttext, 'Night')) continue;

            $forecasts[$forecast->title]['summary'] = $forecast->fcttext;
            $forecasts[$forecast->title]['rain'] = $forecast->pop;
        }

        foreach ($data->forecast->simpleforecast->forecastday as $forecast) {
            if (isset($forecasts[$forecast->date->weekday]['epoch'])) continue;

            $forecasts[$forecast->date->weekday]['epoch'] = $forecast->date->epoch;
            $forecasts[$forecast->date->weekday]['title'] = $forecast->conditions;
            $forecasts[$forecast->date->weekday]['highTemp'] = $forecast->high->fahrenheit;
            $forecasts[$forecast->date->weekday]['lowTemp'] = $forecast->low->fahrenheit;
            $forecasts[$forecast->date->weekday]['humidity'] = $forecast->avehumidity;
            $forecasts[$forecast->date->weekday]['windSpeed'] = $forecast->avewind->mph;
            $forecasts[$forecast->date->weekday]['windDirection'] = $forecast->avewind->dir;
            $forecasts[$forecast->date->weekday]['image'] = $forecast->icon;
            $forecasts[$forecast->date->weekday]['date'] = "{$forecast->date->year}-{$forecast->date->month}-{$forecast->date->day} {$forecast->date->hour}:{$forecast->date->min}:{$forecast->date->sec}";
        }

        $config['hostname'] = "nmteck.info";
        $config['username'] = "root";
        $config['password'] = "3n3rgy";
        $config['database'] = "nmtdev_weather";
        $config['dbdriver'] = "mysql";
        $config['dbprefix'] = "";
        $config['pconnect'] = FALSE;
        $config['db_debug'] = TRUE;
        $config['cache_on'] = FALSE;
        $config['cachedir'] = "";
        $config['char_set'] = "utf8";
        $config['dbcollat'] = "utf8_general_ci";

        $DB =& $this->load->database($config, TRUE);

        foreach ($forecasts as $forecast) {
            if (!isset($forecast['epoch'])) continue;

            $DB->where('epoch', $forecast['epoch']);
            $hasForecast = $DB->get('forecast_data')->row();

            if ($hasForecast == false) {
                $DB->set($forecast);
                $DB->insert('forecast_data');
            }
        }

        echo 'Done';
    }

    // Method: POST, PUT, GET etc
    // Data: array("param" => "value") ==> index.php?param=value

    function CallAPI($method, $url, $data = false)
    {
        $curl = curl_init();

        switch ($method)
        {
            case "POST":
                curl_setopt($curl, CURLOPT_POST, 1);

                if ($data)
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                break;
            case "PUT":
                curl_setopt($curl, CURLOPT_PUT, 1);
                break;
            default:
                if ($data)
                    $url = sprintf("%s?%s", $url, http_build_query($data));
        }

        // Optional Authentication:
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_USERPWD, "username:password");

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        return curl_exec($curl);
    }
}
