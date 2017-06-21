<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$plugin_info       = array(
    'pi_name'        => 'Cat URL',
    'pi_version'     => '1.1',
    'pi_author'      => 'Samuel Coles',
    'pi_description' => 'Gets category name from url then does lookup in db to spit values out for that category i.e category_url_title, category_name, id',
    'pi_usage'       => Cat_url::usage()
);

class Cat_url {

    /* -------------------------------------------------------------------------------
        RETURNS CATEGORY ID FROM CATEGORY_URL_TITLE

        USAGE: {exp:category_id segment="2" site="default_site" category_group="16"}
    ------------------------------------------------------------------------------- */
        public function category_id() {
            $uri_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $uri_segments = explode('/', $uri_path); // Gets segments from url

            // Get Parameters
            $paramSegment = ee()->TMPL->fetch_param('segment'); // REQUIRED - Segment number to use from url www.my-website.com/1/2/3 {exp:category_id segment='2'}
            $paramSite = ee()->TMPL->fetch_param('site'); // site_name when using multiple sites
            $paramCatGroup = ee()->TMPL->fetch_param('category_group'); // Category Group ID to search in

            $segment = $uri_segments[$paramSegment]; // Find selected segment from $uri_segments and store in variable

            // If site_name is specified
            if (isset($paramSite)) {
                $joins .= " LEFT JOIN exp_sites s ON s.site_id = c.site_id"; // Join sites table
                $ands .= " AND s.site_name = '$paramSite'"; // Where site_name === specified site_name
                }

            // If Category Group is specified
            if (isset($paramCatGroup)) {
                $ands .= " AND c.group_id = '$paramCatGroup'"; // Where category_group === x
                }

            /* -------------------------------------------------------------------------------
                BUILD SQL QUERY
            ------------------------------------------------------------------------------- */
                $sql = "SELECT c.cat_id FROM exp_categories c";
                // JOIN
                if (isset($joins)) {$sql .= $joins;}
                // WHERE
                $sql .= " WHERE c.cat_url_title = '$segment'";
                // AND
                if (isset($ands)) {$sql .= $ands;}

            // Run query in DB
            $query = ee()->db->query($sql);

            foreach ($query->result_array() AS $row) {
                return $row['cat_id'];
                }

            }

    /* -------------------------------------------------------------------------------
        RETURNS CATEGORY NAME
    ------------------------------------------------------------------------------- */
    public function category_name() {
        $uri_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri_segments = explode('/', $uri_path);
        $paramSite = ee()->TMPL->fetch_param('site');
        $paramSegment = ee()->TMPL->fetch_param('segment');
        $cat_url_title = $uri_segments[$paramSegment];

        $sql = "SELECT c.cat_name FROM exp_categories c LEFT JOIN exp_sites s ON s.site_id = c.site_id WHERE c.cat_url_title = '$cat_url_title' AND s.site_name = '$paramSite'";
        $query = ee()->db->query($sql);

        foreach ($query->result_array() AS $row) {
            return $row['cat_name'];
            }

        }

    /* -------------------------------------------------------------------------------
        RETURN CATEGORY URL TITLE
    ------------------------------------------------------------------------------- */
    public function category_url_title() {
        $uri_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri_segments = explode('/', $uri_path);
        $parameter = ee()->TMPL->fetch_param('segment');

        return $uri_segments[$parameter];
        }

    /* -------------------------------------------------------------------------------
        RETURNS CATEGORY NAME USING QUERY STRING IN URL
    ------------------------------------------------------------------------------- */
    public function query_string() {
        $uri_path = parse_url($_SERVER['QUERY_STRING'], PHP_URL_PATH);
        $paramSite = ee()->TMPL->fetch_param('site');
        $paramQuery = ee()->TMPL->fetch_param('query');
        $queries = explode('&', $uri_path);

        foreach($queries AS $query) {

            if($query == strstr($query, $paramQuery)) {
                $string = explode('=', $query);

                $sql = "SELECT c.cat_name FROM exp_categories c LEFT JOIN exp_sites s ON s.site_id = c.site_id WHERE c.cat_url_title = '$string[1]' AND s.site_name = '$paramSite'";
                $sql_query = ee()->db->query($sql);

                foreach ($sql_query->result_array() AS $row) {
                    return $row['cat_name'];
                    }

                }

            }

        }

    /* -------------------------------------------------------------------------------
        PLUGIN USAGE
    ------------------------------------------------------------------------------- */

    function usage() {
        ob_start();
        ?>

        Lookup category details by pointing this plugin at a segment in the url.

        If url is: http://ihasco.co.uk/approved-training/cpd <= segment="2" would lookup cpd in exp_categories table

        {exp:cat_url:category_id segment="2"}
            // Spits out the category ID

        {exp:cat_url:category_name segment="2"}
            // Spits out the category Name

        {exp:cat_url:category_url_title segment="2"}
            // Spits out the category URL Title (Could probably use {segment_2} instead for this as does the same thing.)

        <?php
        $buffer = ob_get_contents();

        ob_end_clean();

        return $buffer;
        }

    }

/* End of file pi.cat_url.php */
/* Location: ./system/expressionengine/third_party/cat_url/pi.cat_url.php */
