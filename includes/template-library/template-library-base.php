<?php

namespace ElementPack\Includes\TemplateLibrary;

if (!defined('ABSPATH')) exit; // Exit if accessed directly
class ElementPack_Template_Library_Base {

    protected $perPage = 18;
    protected $totalPage = 1;
    protected $demo_total = 0;
    protected $searchVal;
    protected $termSlug;
    protected $demoType;
    protected $sortByTitle;
    protected $sortByDate;

    protected $table_cat;
    protected $table_post;
    protected $table_cat_post;
    protected $charset_collate;
    protected $wpdb;

    // public $packLicenseActivated = true;
    public $packLicenseActivated = false;


    protected $api_url = 'https://www.elementpack.pro/data/templates.json';

    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->charset_collate = $this->wpdb->get_charset_collate();
        $this->table_cat = $this->wpdb->prefix . 'ep_template_library_cat';
        $this->table_post = $this->wpdb->prefix . 'ep_template_library_post';
        $this->table_cat_post = $this->wpdb->prefix . 'ep_template_library_cat_post';
        $this->packLicenseActivated = element_pack_pro_activated();
    }


    public function createTemplateTables() {
        $charset_collate = $this->charset_collate;
        $table_cat_name = $this->table_cat;
        $table_post_name = $this->table_post;
        $table_cat_post_name = $this->table_cat_post;;
        if (defined('BDTEP_TPL_DB_VER') && BDTEP_TPL_DB_VER != get_option('BDTEP_TPL_DB_VER', false)) {
            $this->wpdb->query("DROP TABLE IF EXISTS $table_cat_name");
            $this->wpdb->query("DROP TABLE IF EXISTS $table_post_name");
            $this->wpdb->query("DROP TABLE IF EXISTS $table_cat_post_name");
            update_option('BDTEP_TPL_DB_VER', BDTEP_TPL_DB_VER);
        }


        $catsql = "CREATE TABLE IF NOT EXISTS $table_cat_name (
            `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            `term_id` bigint(20) UNSIGNED NOT NULL,
            `name` varchar(191) default NULL,
            `slug` varchar(191) default NULL,
            `description` text default NULL,
            `total` mediumint(9) default NULL,
            `image_url` varchar(191) default NULL,
            UNIQUE KEY id (id),
            UNIQUE (slug)
        ) $charset_collate;";

        $catPostsql = "CREATE TABLE IF NOT EXISTS $table_cat_post_name (
            `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            `term_id` bigint(20) UNSIGNED NOT NULL,
            `demo_id` bigint(20) UNSIGNED NOT NULL,
            UNIQUE KEY id (id)
        ) $charset_collate;";

        $postsql = "CREATE TABLE IF NOT EXISTS $table_post_name (
            `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            `demo_id` bigint(20) UNSIGNED NOT NULL,
            `date` date default NULL,
            `title` varchar(191) default NULL,
            `short_desc` text default NULL,
            `is_pro` int(2) default NULL,
            `type` int(2) default NULL,
            `thumbnail` varchar(191) default NULL,
            `demo_url` varchar(191) default NULL,
            `json_url` varchar(191) default NULL,
            UNIQUE KEY id (id),
            UNIQUE (demo_id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($catsql);
        dbDelta($postsql);
        dbDelta($catPostsql);
        $this->setTemplateDataToDB();
    }

    public function setTemplateDataToDB() {

        $demoData = $this->remote_get_demo_data();
        if ($demoData) {
            $this->wpdb->query('TRUNCATE ' . $this->table_cat);
            $this->wpdb->query('TRUNCATE ' . $this->table_post);
            $this->wpdb->query('TRUNCATE ' . $this->table_cat_post);

            $prefixCat = "INSERT INTO `" . $this->table_cat . "` (`term_id`, `name`, `slug`, `description`, `total`, `image_url`) VALUES ";
            $CatQueryString = [];

            $prefixPost = "INSERT INTO `" . $this->table_post . "` (`demo_id`, `date`, `title`, `short_desc`, `is_pro`, `type`, `thumbnail`, `demo_url`, `json_url`) VALUES ";
            $PostQueryString = [];

            $prefixPostCat = "INSERT INTO `" . $this->table_cat_post . "` (`term_id`, `demo_id`) VALUES ";
            $PostCatQueryString = [];

            foreach ($demoData as $demo) {
                $Catstring = $this->wpdb->prepare(
                    "(%d, %s, %s, %s, %d, %s)",
                    intval($demo['term_id']),
                    sanitize_text_field($demo['name']),
                    sanitize_title($demo['slug']),
                    sanitize_textarea_field($demo['description']),
                    intval($demo['total']),
                    esc_url_raw($demo['image_url'])
                );
                array_push($CatQueryString, $Catstring);

                if (isset($demo['data']) && is_array($demo['data'])) {
                    $postData = $demo['data'];
                    foreach ($postData as $post) {
                        $PostQueryString[$post['demo_id']] = $this->wpdb->prepare(
                            "(%d, %s, %s, %s, %d, %d, %s, %s, %s)",
                            intval($post['demo_id']),
                            sanitize_text_field($post['date']),
                            sanitize_text_field($post['title']),
                            sanitize_textarea_field($post['short_desc']),
                            intval($post['is_pro']),
                            intval($post['type']),
                            esc_url_raw($this->normalize_remote_url($post['thumbnail'])),
                            esc_url_raw($post['demo_url']),
                            esc_url_raw($this->normalize_remote_url($post['json_url']))
                        );

                        $PostCatQueryString[] = $this->wpdb->prepare(
                            "(%d, %d)",
                            intval($demo['term_id']),
                            intval($post['demo_id'])
                        );
                    }
                }
            }

            $wpdbError = false;

            $query = $prefixCat . implode(',', $CatQueryString);
            $this->wpdb->query($query);
            if ($this->wpdb->last_error) {
                $wpdbError = true;
            }

            $PostQueryString = array_chunk($PostQueryString, 100, true);
            foreach ($PostQueryString as $chunk) {
                $postQuery = $prefixPost . implode(',', $chunk);
                $this->wpdb->query($postQuery);
            }

            if ($this->wpdb->last_error) {
                $wpdbError = true;
            }

            $PostCatQueryString = array_chunk($PostCatQueryString, 100, true);
            foreach ($PostCatQueryString as $chunk) {
                $postCatQuery = $prefixPostCat . implode(',', $chunk);
                $this->wpdb->query($postCatQuery);
            }

            if ($this->wpdb->last_error) {
                $wpdbError = true;
            }

            if (!$wpdbError) {
                set_transient($this->get_transient_key(), 1, DAY_IN_SECONDS * 15);
            }
        }
    }


    public function get_transient_key() {
        $var = '';
        if (defined('BDTEP_TPL_DB_VER')) {
            $var = BDTEP_TPL_DB_VER;
        }
        return 'ep_elements_demo_import_table_data_' . $var;
    }

    public function checkDemoData() {

        $result = $this->wpdb->get_row("SHOW TABLES LIKE '" . $this->table_cat . "'", ARRAY_A);
        $tableExists = false;
        if (is_array($result)) {
            if (count($result) == 1) {
                $tableExists = true;
            }
        }

        $demoData = get_transient($this->get_transient_key());
        $needsRefresh = !$demoData || !$tableExists;

        if (defined('BDTEP_TPL_DB_VER') && BDTEP_TPL_DB_VER !== get_option('BDTEP_TPL_DB_VER', false)) {
            $needsRefresh = true;
            delete_transient($this->get_transient_key());
        }

        if ($needsRefresh) {
            $this->createTemplateTables();
        }
    }

    /**
     * @return array|mixed
     * retrieve element pack categories from remote server with api route
     */
    public function remote_get_demo_data() {
        $response = wp_remote_get($this->api_url, ['timeout' => 60, 'sslverify' => true]);
        $body = wp_remote_retrieve_body($response);
        $body = json_decode($body, true);

        return $body;
    }

    protected function normalize_remote_url( $url ) {
        if ( empty( $url ) ) {
            return $url;
        }

        if ( 0 === strpos( $url, 'http://' ) || 0 === strpos( $url, 'https://' ) ) {
            return $url;
        }

        if ( 0 === strpos( $url, '/' ) ) {
            return 'https://www.elementpack.pro' . $url;
        }

        return $url;
    }

    public function getNaviationItems() {

        $this->checkDemoData();
        $demoData = $this->wpdb->get_results("SELECT * FROM {$this->table_cat}", ARRAY_A);

        $navItems = array();
        $totalDemo = 0;
        foreach ($demoData as $data) {
            $total = intval($data['total']);
            $totalDemo = $totalDemo + $total;
            $navItems[] = array('term_slug' => $data['slug'], 'term_name' => $data['name'], 'term_id' => $data['term_id'], 'count' => $total);
        }
        $this->demo_total = $totalDemo;
        $firstItem = array('term_slug' => 'demo_term_all', 'term_name' => 'All Templates', 'term_id' => 0, 'count' => $totalDemo);

        return array_merge([$firstItem], $navItems);
    }

    public function getData($paged = 0) {

        $per_page   = $this->perPage;
        $slug       = $this->termSlug;
        $search     = $this->searchVal;
        $demo_tab   = $this->demoType;

        $sortingQuery = '';
        $orderbyTitle       = 'title';
        $sortByTitleType    = $this->sortByTitle;
        if ($sortByTitleType === 'asc' || $sortByTitleType === 'desc') {
            $sortingQuery = " ORDER BY " . $orderbyTitle . " " . $sortByTitleType;
        }

        $orderbyDate        = 'demo_id';
        $sortDateType       = $this->sortByDate;
        if ($sortDateType === 'asc' || $sortDateType === 'desc') {
            if ($sortingQuery) {
                $sortingQuery .= ", " . $orderbyDate . " " . $sortDateType;
            } else {
                $sortingQuery = " ORDER BY " . $orderbyDate . " " . $sortDateType;
            }
        }

        if (!$sortingQuery) {
            $sortingQuery = " ORDER BY " . $orderbyDate . " desc";
        }

        if ($demo_tab) {
            if ($demo_tab == 'pro') {
                $demo_tab = 1;
            } elseif ($demo_tab == 'free') {
                $demo_tab = 0;
            } else {
                $demo_tab = '';
            }
        }

        if ($slug == 'demo_term_all') {
            $slug = false;
        }

        $where_clauses = [];
        $prepare_values = [];

        if ($search) {
            $escaped_search = $this->wpdb->esc_like($search);
            $where_clauses[] = "title LIKE %s";
            $prepare_values[] = '%' . $escaped_search . '%';
        }

        if ($slug) {
            $where_clauses[] = "slug = %s";
            $prepare_values[] = $slug;
        }

        if ($demo_tab === 1 || $demo_tab === 0) {
            $where_clauses[] = "is_pro = %d";
            $prepare_values[] = $demo_tab;
        }

        $keywordSearch = '';
        if (!empty($where_clauses)) {
            $keywordSearch = " WHERE " . implode(" AND ", $where_clauses);
        }

        $postTable      = $this->table_post;
        $postCatTable   = $this->table_cat_post;
        $catTable       = $this->table_cat;

        $count_sql = "SELECT COUNT(DISTINCT {$postTable}.demo_id) FROM {$postTable}
 LEFT JOIN {$postCatTable} ON {$postTable}.demo_id = {$postCatTable}.demo_id
LEFT JOIN {$catTable} ON {$catTable}.term_id = {$postCatTable}.term_id
 {$keywordSearch}";

        if (!empty($prepare_values)) {
            $count_sql = $this->wpdb->prepare($count_sql, $prepare_values);
        }

        $total_items = $this->wpdb->get_var($count_sql);

        $this->totalPage = ceil($total_items / $per_page);
        $offset = absint($paged * $per_page);
        $per_page = absint($per_page);

        $data_sql = "SELECT {$postTable}.*,{$postTable}.thumbnail as preview, {$postCatTable}.term_id, {$catTable}.slug as categories,{$catTable}.slug FROM {$postTable}
 LEFT JOIN {$postCatTable} ON {$postTable}.demo_id = {$postCatTable}.demo_id
LEFT JOIN {$catTable} ON {$catTable}.term_id = {$postCatTable}.term_id
{$keywordSearch} GROUP BY {$postTable}.demo_id {$sortingQuery} LIMIT %d, %d";

        $data_prepare_values = array_merge($prepare_values, [$offset, $per_page]);
        $data_sql = $this->wpdb->prepare($data_sql, $data_prepare_values);

        $allPagesData = $this->wpdb->get_results($data_sql, ARRAY_A);

        return $allPagesData;
    }


    public function getElementorLibraryData($paged = 0) {

        $per_page   = $this->perPage;
        $slug       = $this->termSlug;
        $search     = $this->searchVal;
        $demo_tab   = $this->demoType;

        $sortingQuery = '';
        $orderbyTitle       = 'title';
        $sortByTitleType    = $this->sortByTitle;
        if ($sortByTitleType === 'asc' || $sortByTitleType === 'desc') {
            $sortingQuery = " ORDER BY " . $orderbyTitle . " " . $sortByTitleType;
        }

        $orderbyDate        = 'demo_id';
        $sortDateType       = $this->sortByDate;
        if ($sortDateType === 'asc' || $sortDateType === 'desc') {
            if ($sortingQuery) {
                $sortingQuery .= ", " . $orderbyDate . " " . $sortDateType;
            } else {
                $sortingQuery = " ORDER BY " . $orderbyDate . " " . $sortDateType;
            }
        }

        if (!$sortingQuery) {
            $sortingQuery = " ORDER BY " . $orderbyDate . " desc";
        }

        if ($slug == 'demo_term_all') {
            $slug = false;
        }

        $where_clauses = [];
        $prepare_values = [];

        if ($search) {
            $escaped_search = $this->wpdb->esc_like($search);
            $where_clauses[] = "title LIKE %s";
            $prepare_values[] = '%' . $escaped_search . '%';
        }

        if ($slug) {
            $where_clauses[] = "slug = %s";
            $prepare_values[] = $slug;
        }

        $where_clauses[] = "type = %d";
        $prepare_values[] = intval($demo_tab);

        $keywordSearch = '';
        if (!empty($where_clauses)) {
            $keywordSearch = " WHERE " . implode(" AND ", $where_clauses);
        }

        $postTable      = $this->table_post;
        $postCatTable   = $this->table_cat_post;
        $catTable       = $this->table_cat;

        $count_sql = "SELECT COUNT(DISTINCT {$postTable}.demo_id) FROM {$postTable}
 LEFT JOIN {$postCatTable} ON {$postTable}.demo_id = {$postCatTable}.demo_id
LEFT JOIN {$catTable} ON {$catTable}.term_id = {$postCatTable}.term_id
 {$keywordSearch}";

        if (!empty($prepare_values)) {
            $count_sql = $this->wpdb->prepare($count_sql, $prepare_values);
        }

        $total_items = $this->wpdb->get_var($count_sql);

        $this->totalPage = ceil($total_items / $per_page);
        $offset = absint($paged * $per_page);
        $per_page = absint($per_page);

        $data_sql = "SELECT {$postCatTable}.term_id,{$postCatTable}.id,
{$postTable}.id as template_id,{$postTable}.demo_id,DATE_FORMAT({$postTable}.date, \"%%Y%%m%%d\") as date,{$postTable}.title,{$postTable}.short_desc,
{$postTable}.is_pro,{$postTable}.type,{$postTable}.thumbnail,{$postTable}.demo_url,{$postTable}.json_url,
 {$catTable}.slug as categories, {$postTable}.is_pro as source FROM {$postTable}
 LEFT JOIN {$postCatTable} ON {$postTable}.demo_id = {$postCatTable}.demo_id
LEFT JOIN {$catTable} ON {$catTable}.term_id = {$postCatTable}.term_id
{$keywordSearch} {$sortingQuery} LIMIT %d, %d";

        $data_prepare_values = array_merge($prepare_values, [$offset, $per_page]);
        $data_sql = $this->wpdb->prepare($data_sql, $data_prepare_values);

        $allPagesData = $this->wpdb->get_results($data_sql, ARRAY_A);

        return $allPagesData;
    }

    public function findDemo($id) {
        return $this->wpdb->get_row(
            $this->wpdb->prepare("SELECT * FROM {$this->table_post} WHERE id=%d", $id),
            ARRAY_A
        );
    }
}
