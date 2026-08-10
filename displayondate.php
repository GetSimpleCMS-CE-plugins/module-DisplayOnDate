<?php
/**
 * Module Name: Display On Date
 * Module ID: displayondate
 * Description: Lists DisplayOnDate scheduled blocks with start/end dates and status.
 * Version: 1.0
 * Default W: 6
 * Default H: 5
 */

if (!defined('IN_GS')) { die('You cannot load this page directly.'); }

$i18n_m = dash_module_i18n('displayondate');

// Unique scoped ID so styles don't bleed into other modules
$uid = 'dond_' . substr(md5(__FILE__), 0, 6);

// Check DisplayOnDate plugin is active
$plugin_ok = function_exists('displayon_get_blocks');

$blocks = [];

if ($plugin_ok) {
    try {
        $blocks = displayon_get_blocks();
    } catch (\Throwable $e) {
        $blocks = [];
    }
}

$edit_base = 'load.php?id=DisplayOnDate';
$new_url   = $edit_base . '&add';

// Sort: active first, then upcoming, then expired - each group by soonest end/start
$now = time();
uasort($blocks, function ($a, $b) use ($now) {
    $rank = function ($blk) use ($now) {
        $start = strtotime($blk['start']);
        $end   = strtotime($blk['end']);
        if ($now < $start) return 1; // upcoming
        if ($now > $end)   return 2; // expired
        return 0;                    // active
    };
    $ra = $rank($a);
    $rb = $rank($b);
    if ($ra !== $rb) return $ra <=> $rb;
    return strtotime($a['end']) <=> strtotime($b['end']);
});
?>

<style>
#<?php echo $uid ?> .dond-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 8px;
}
#<?php echo $uid ?> .dond-header h3 {
    margin: 0;
    display: flex;
    align-items: center;
    gap: 6px;
}
#<?php echo $uid ?> .dond-btn-new {
    display: inline-block;
    background: #0094f0;
    color: #fff;
    font-size: 12px;
    padding: 4px 10px;
    border-radius: 4px;
    text-decoration: none;
    white-space: nowrap;
}
#<?php echo $uid ?> .dond-btn-new:hover { background: #007acc; }
#<?php echo $uid ?> .dond-table-wrap { max-height: 260px; overflow-y: auto; }
#<?php echo $uid ?> .dond-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 12px;
}
#<?php echo $uid ?> .dond-table th {
    text-align: left;
    padding: 5px 8px;
    border-bottom: 2px solid #eee;
    color: #888;
    font-weight: 600;
    white-space: nowrap;
    position: sticky;
    top: 0;
    background: #fff;
    z-index: 1;
}
#<?php echo $uid ?> .dond-table td {
    padding: 5px 8px;
    border-bottom: 1px solid #f3f3f3;
    vertical-align: middle;
}
#<?php echo $uid ?> .dond-table tr:last-child td { border-bottom: none; }
#<?php echo $uid ?> .dond-table tr:hover td { background: #fafafa; }
#<?php echo $uid ?> .dond-title {
    font-weight: 500;
    color: #333;
    max-width: 160px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
#<?php echo $uid ?> .dond-date {
    color: #666;
    font-family: monospace;
    font-size: 11px;
    white-space: nowrap;
}
#<?php echo $uid ?> .dond-status {
    display: inline-block;
    padding: 2px 8px;
    border-radius: 10px;
    font-size: 11px;
    font-weight: 600;
    white-space: nowrap;
}
#<?php echo $uid ?> .dond-status--active   { background: #d4edda; color: #155724; }
#<?php echo $uid ?> .dond-status--upcoming { background: #fff3cd; color: #856404; }
#<?php echo $uid ?> .dond-status--expired  { background: #f1f1f1; color: #888; }
#<?php echo $uid ?> .dond-actions { white-space: nowrap; text-align: center; }
#<?php echo $uid ?> .dond-btn {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 3px 8px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 600;
    text-decoration: none;
    border: none;
    cursor: pointer;
}
#<?php echo $uid ?> .dond-btn-edit {
    background: #e8702a;
    color: #fff;
}
#<?php echo $uid ?> .dond-btn-edit:hover { background: #cf5f20; }
#<?php echo $uid ?> .dond-empty {
    color: #bbb;
    font-style: italic;
    text-align: center;
    padding: 16px;
}
#<?php echo $uid ?> .dond-missing {
    color: #856404;
    background: #fff3cd;
    border: 1px solid #ffeeba;
    border-radius: 6px;
    padding: 10px 12px;
    font-size: 13px;
}
</style>

<div id="<?php echo $uid ?>">
    <div class="dond-header">
        <h3>
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" style="flex-shrink:0">
                <path d="M0 0h24v24H0z" fill="none"/>
                <path fill="currentColor" d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zM7 12h5v5H7z"/>
            </svg>
            <?php echo $i18n_m('dond_lang_Title'); ?>
        </h3>
        <?php if ($plugin_ok): ?>
        <a class="dond-btn-new" href="<?php echo $new_url; ?>">+ <?php echo $i18n_m('dond_lang_New_Block'); ?></a>
        <?php endif; ?>
    </div>

    <?php if (!$plugin_ok): ?>
        <p class="dond-missing">&#9888; <?php echo $i18n_m('dond_lang_Plugin_Not_Active'); ?></p>
    <?php elseif (empty($blocks)): ?>
        <p class="dond-empty"><?php echo $i18n_m('dond_lang_No_Blocks'); ?></p>
    <?php else: ?>
    <div class="dond-table-wrap">
        <table class="dond-table">
            <tr>
                <th><?php echo $i18n_m('dond_lang_Key'); ?></th>
                <th><?php echo $i18n_m('dond_lang_Start'); ?></th>
                <th><?php echo $i18n_m('dond_lang_End'); ?></th>
                <th><?php echo $i18n_m('dond_lang_Status'); ?></th>
                <th style="text-align:center"><?php echo $i18n_m('dond_lang_Action'); ?></th>
            </tr>
            <?php foreach ($blocks as $key => $b):
                $title = htmlspecialchars($key, ENT_QUOTES);
                $start = htmlspecialchars($b['start'] ?? '', ENT_QUOTES);
                $end   = htmlspecialchars($b['end'] ?? '', ENT_QUOTES);

                $start_ts = strtotime($b['start'] ?? '');
                $end_ts   = strtotime($b['end'] ?? '');

                if ($now < $start_ts) {
                    $status_class = 'upcoming';
                    $status_label = $i18n_m('dond_lang_Upcoming');
                } elseif ($now > $end_ts) {
                    $status_class = 'expired';
                    $status_label = $i18n_m('dond_lang_Expired');
                } else {
                    $status_class = 'active';
                    $status_label = $i18n_m('dond_lang_Active');
                }

                $edit_url = $edit_base . '&edit=' . urlencode($key);
            ?>
            <tr>
                <td class="dond-title" title="<?php echo $title; ?>"><?php echo $title; ?></td>
                <td class="dond-date"><?php echo $start; ?></td>
                <td class="dond-date"><?php echo $end; ?></td>
                <td>
                    <span class="dond-status dond-status--<?php echo $status_class; ?>">
                        <?php echo $status_label; ?>
                    </span>
                </td>
                <td class="dond-actions">
                    <a class="dond-btn dond-btn-edit" href="<?php echo $edit_url; ?>"
                       title="<?php echo $i18n_m('dond_lang_Edit'); ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 16 16">
                            <path fill="#fff" d="M10.529 1.764a2.621 2.621 0 1 1 3.707 3.707l-.779.779L9.75 2.543zM9.043 3.25L2.657 9.636a2.96 2.96 0 0 0-.772 1.354l-.87 3.386a.5.5 0 0 0 .61.608l3.385-.869a2.95 2.95 0 0 0 1.354-.772l6.386-6.386z"/>
                        </svg>
                        <?php echo $i18n_m('dond_lang_Edit'); ?>
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
    <?php endif; ?>
</div>
