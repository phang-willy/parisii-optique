<?php
/**
 * Admin list of contact messages with search and sortable columns
 *
 * @package Parisii_Optique_Plugin
 */

if (!defined('ABSPATH')) {
    exit;
}

class Parisii_Optique_Contact_List {

    /** @var string[] Allowed orderby columns */
    private static $orderby_allowed = array('id', 'date_created', 'read_flag', 'nom', 'prenom', 'email', 'sujet');

    public function render() {
        global $wpdb;
        $table = $wpdb->prefix . 'po_contact';
        $per_page = 20;
        $paged = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
        $offset = ($paged - 1) * $per_page;
        $search = isset($_GET['s']) ? trim(sanitize_text_field($_GET['s'])) : '';
        $orderby = isset($_GET['orderby']) ? sanitize_text_field($_GET['orderby']) : 'date_created';
        $order = isset($_GET['order']) ? strtoupper(sanitize_text_field($_GET['order'])) : 'DESC';
        if (!in_array($orderby, self::$orderby_allowed, true)) {
            $orderby = 'date_created';
        }
        if ($order !== 'ASC' && $order !== 'DESC') {
            $order = 'DESC';
        }
        $next_order = $order === 'ASC' ? 'DESC' : 'ASC';

        $where = '1=1';
        $where_values = array();
        if ($search !== '') {
            $like = '%' . $wpdb->esc_like($search) . '%';
            $where = "(
                nom LIKE %s OR prenom LIKE %s OR email LIKE %s
                OR tel LIKE %s OR sujet LIKE %s OR message LIKE %s
            )";
            $where_values = array_fill(0, 6, $like);
        }

        $total = (int) $wpdb->get_var(
            $where_values
                ? $wpdb->prepare("SELECT COUNT(*) FROM $table WHERE $where", $where_values)
                : "SELECT COUNT(*) FROM $table WHERE $where"
        );

        $orderby_safe = array_key_exists($orderby, array_flip(self::$orderby_allowed)) ? $orderby : 'date_created';
        $order_safe = $order === 'ASC' ? 'ASC' : 'DESC';
        $sql = "SELECT id, nom, prenom, email, tel, sujet, LEFT(message, 80) AS message_preview, date_created, read_flag
                FROM $table
                WHERE $where
                ORDER BY `$orderby_safe` $order_safe
                LIMIT %d OFFSET %d";
        $query_params = array_merge($where_values, array($per_page, $offset));
        $items = $wpdb->get_results($wpdb->prepare($sql, ...$query_params));
        if (!is_array($items)) {
            $items = array();
        }

        $base_url = admin_url('admin.php');
        $query_args = array('page' => 'parisii-optique-contact', 'tab' => 'list');
        if ($search !== '') {
            $query_args['s'] = $search;
        }
        $sort_link = function ($col) use ($orderby, $order, $next_order, $base_url, $query_args) {
            $query_args['orderby'] = $col;
            $query_args['order'] = $next_order;
            return add_query_arg($query_args, $base_url);
        };
        $sort_class = function ($col) use ($orderby, $order) {
            if ($orderby !== $col) {
                return 'sortable';
            }
            return 'sortable ' . strtolower($order);
        };
        ?>
        <div class="tablenav top">
            <form method="get" class="search-form">
                <input type="hidden" name="page" value="parisii-optique-contact">
                <input type="hidden" name="tab" value="list">
                <?php if ($orderby !== 'date_created' || $order !== 'DESC') : ?>
                    <input type="hidden" name="orderby" value="<?php echo esc_attr($orderby); ?>">
                    <input type="hidden" name="order" value="<?php echo esc_attr($order); ?>">
                <?php endif; ?>
                <p class="search-box">
                    <label class="screen-reader-text" for="contact-search-input"><?php esc_html_e('Rechercher des messages:', 'parisii-optique-plugin'); ?></label>
                    <input type="search" id="contact-search-input" name="s" value="<?php echo esc_attr($search); ?>" placeholder="<?php esc_attr_e('Rechercher...', 'parisii-optique-plugin'); ?>">
                    <input type="submit" id="search-submit" class="button" value="<?php esc_attr_e('Rechercher', 'parisii-optique-plugin'); ?>">
                </p>
            </form>
        </div>

        <table class="wp-list-table widefat fixed striped parisii-optique-contact-table">
            <thead>
                <tr>
                    <th scope="col" class="column-id manage-column sortable <?php echo esc_attr($sort_class('id')); ?>">
                        <a href="<?php echo esc_url($sort_link('id')); ?>">
                            <span><?php esc_html_e('ID', 'parisii-optique-plugin'); ?></span>
                            <span class="sorting-indicator"></span>
                        </a>
                    </th>
                    <th scope="col" class="manage-column sortable <?php echo esc_attr($sort_class('date_created')); ?>">
                        <a href="<?php echo esc_url($sort_link('date_created')); ?>">
                            <span><?php esc_html_e('Date', 'parisii-optique-plugin'); ?></span>
                            <span class="sorting-indicator"></span>
                        </a>
                    </th>
                    <th scope="col" class="manage-column sortable <?php echo esc_attr($sort_class('nom')); ?>">
                        <a href="<?php echo esc_url($sort_link('nom')); ?>">
                            <span><?php esc_html_e('Nom', 'parisii-optique-plugin'); ?></span>
                            <span class="sorting-indicator"></span>
                        </a>
                    </th>
                    <th scope="col" class="manage-column sortable <?php echo esc_attr($sort_class('prenom')); ?>">
                        <a href="<?php echo esc_url($sort_link('prenom')); ?>">
                            <span><?php esc_html_e('Prénom', 'parisii-optique-plugin'); ?></span>
                            <span class="sorting-indicator"></span>
                        </a>
                    </th>
                    <th scope="col" class="manage-column sortable <?php echo esc_attr($sort_class('email')); ?>">
                        <a href="<?php echo esc_url($sort_link('email')); ?>">
                            <span><?php esc_html_e('Email', 'parisii-optique-plugin'); ?></span>
                            <span class="sorting-indicator"></span>
                        </a>
                    </th>
                    <th scope="col" class="manage-column"><?php esc_html_e('Tél', 'parisii-optique-plugin'); ?></th>
                    <th scope="col" class="manage-column sortable <?php echo esc_attr($sort_class('sujet')); ?>">
                        <a href="<?php echo esc_url($sort_link('sujet')); ?>">
                            <span><?php esc_html_e('Sujet', 'parisii-optique-plugin'); ?></span>
                            <span class="sorting-indicator"></span>
                        </a>
                    </th>
                    <th scope="col" class="manage-column"><?php esc_html_e('Message (aperçu)', 'parisii-optique-plugin'); ?></th>
                    <th scope="col" class="manage-column sortable <?php echo esc_attr($sort_class('read_flag')); ?>">
                        <a href="<?php echo esc_url($sort_link('read_flag')); ?>">
                            <span><?php esc_html_e('Lu', 'parisii-optique-plugin'); ?></span>
                            <span class="sorting-indicator"></span>
                        </a>
                    </th>
                    <th scope="col" class="manage-column"><?php esc_html_e('Actions', 'parisii-optique-plugin'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($items)) : ?>
                    <tr>
                        <td colspan="10" class="no-items"><?php esc_html_e('Aucun message.', 'parisii-optique-plugin'); ?></td>
                    </tr>
                <?php else : ?>
                    <?php foreach ($items as $row) : ?>
                        <tr>
                            <td class="column-id" data-colname="<?php esc_attr_e('ID', 'parisii-optique-plugin'); ?>"><?php echo esc_html($row->id); ?></td>
                            <td data-colname="<?php esc_attr_e('Date', 'parisii-optique-plugin'); ?>"><?php echo esc_html(mysql2date(get_option('date_format') . ' à ' . get_option('time_format'), $row->date_created)); ?></td>
                            <td data-colname="<?php esc_attr_e('Nom', 'parisii-optique-plugin'); ?>"><?php echo esc_html($row->nom); ?></td>
                            <td data-colname="<?php esc_attr_e('Prénom', 'parisii-optique-plugin'); ?>"><?php echo esc_html($row->prenom); ?></td>
                            <td data-colname="<?php esc_attr_e('Email', 'parisii-optique-plugin'); ?>"><a href="mailto:<?php echo esc_attr($row->email); ?>"><?php echo esc_html($row->email); ?></a></td>
                            <td data-colname="<?php esc_attr_e('Tél', 'parisii-optique-plugin'); ?>"><?php echo esc_html($row->tel); ?></td>
                            <td data-colname="<?php esc_attr_e('Sujet', 'parisii-optique-plugin'); ?>">
                                <a href="<?php echo esc_url(admin_url('admin.php?page=parisii-optique-contact&tab=view&id=' . (int) $row->id)); ?>"><?php echo esc_html($row->sujet); ?></a>
                            </td>
                            <td data-colname="<?php esc_attr_e('Message (aperçu)', 'parisii-optique-plugin'); ?>"><?php echo esc_html($row->message_preview); ?><?php echo strlen($row->message_preview ?? '') >= 80 ? '…' : ''; ?></td>
                            <td data-colname="<?php esc_attr_e('Lu', 'parisii-optique-plugin'); ?>">
                                <?php if (!empty($row->read_flag)) : ?>
                                    <span class="dashicons dashicons-yes-alt" style="color: #46b450;" aria-label="<?php esc_attr_e('Oui', 'parisii-optique-plugin'); ?>"></span>
                                <?php else : ?>
                                    <span class="dashicons dashicons-dismiss" style="color: #dc3232;" aria-label="<?php esc_attr_e('Non', 'parisii-optique-plugin'); ?>"></span>
                                <?php endif; ?>
                            </td>
                            <td data-colname="<?php esc_attr_e('Actions', 'parisii-optique-plugin'); ?>">
                                <a href="<?php echo esc_url(admin_url('admin.php?page=parisii-optique-contact&tab=view&id=' . (int) $row->id)); ?>"><?php esc_html_e('Voir', 'parisii-optique-plugin'); ?></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        <?php
        $total_pages = max(1, (int) ceil($total / $per_page));
        if ($total_pages > 1) {
            echo '<div class="tablenav bottom"><div class="tablenav-pages">';
            echo paginate_links(array(
                'base'      => add_query_arg(array('paged' => '%#%', 'page' => 'parisii-optique-contact', 'tab' => 'list', 's' => $search, 'orderby' => $orderby, 'order' => $order)),
                'format'    => '',
                'prev_text' => '&laquo;',
                'next_text' => '&raquo;',
                'total'     => $total_pages,
                'current'   => $paged,
            ));
            echo '</div></div>';
        }
    }
}
