<?php
/**
 * [ALL] Modal shell and form-field helpers.
 *
 *   gms_modal_open('worker-modal', 'Register worker', 'md');   // sizes: sm | md | lg
 *   ... form markup, gms_field(...) ...
 *   gms_modal_close('<button ...>Save</button>');             // footer HTML (optional)
 *
 * Open/close from JS with UI.modal('worker-modal').open(), or declaratively with
 * data-modal-open="worker-modal" / data-modal-close on any button (see ui.js).
 */
require_once __DIR__ . '/../config.php';

if (!function_exists('gms_modal_open')) {
    function gms_modal_open(string $id, string $title, string $size = 'md', string $subtitle = ''): void
    {
        $widths = ['sm' => 'max-w-md', 'md' => 'max-w-xl', 'lg' => 'max-w-3xl'];
        $w = $widths[$size] ?? $widths['md'];
        ?>
<div id="<?= gms_e($id) ?>" class="modal-backdrop" hidden role="dialog" aria-modal="true" aria-labelledby="<?= gms_e($id) ?>-title">
  <div class="modal-panel <?= $w ?>">
    <div class="modal-head">
      <div>
        <h2 id="<?= gms_e($id) ?>-title" class="h text-2xl sm:text-3xl"><?= gms_e($title) ?></h2>
        <?php if ($subtitle !== ''): ?><p class="mt-1 text-sm text-slate-500"><?= gms_e($subtitle) ?></p><?php endif; ?>
      </div>
      <button type="button" class="modal-x" data-modal-close aria-label="Close dialog"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="modal-body">
<?php
    }
}

if (!function_exists('gms_modal_close')) {
    function gms_modal_close(string $footerHtml = ''): void
    {
        ?>
    </div>
    <?php if ($footerHtml !== ''): ?><div class="modal-foot"><?= $footerHtml ?></div><?php endif; ?>
  </div>
</div>
<?php
    }
}

if (!function_exists('gms_field')) {
    /**
     * Label + control + hint + error slot.
     * $o: type (text|email|tel|date|number|password|select|textarea), required, id, class, hint,
     *     placeholder, rows, options (list or value=>label map, for select), attrs (raw attribute string).
     */
    function gms_field(string $name, string $label, array $o = []): void
    {
        $type  = $o['type'] ?? 'text';
        $id    = $o['id'] ?? $name;
        $req   = !empty($o['required']);
        $attrs = ($req ? ' required' : '') . ' ' . ($o['attrs'] ?? '');
        $ph    = isset($o['placeholder']) ? ' placeholder="' . gms_e($o['placeholder']) . '"' : '';
        echo '<div class="field ' . gms_e($o['class'] ?? '') . '">';
        echo '<label class="label" for="' . gms_e($id) . '">' . gms_e($label);
        if ($req) {
            echo ' <span class="text-red-600" aria-hidden="true">*</span>';
        }
        echo '</label>';
        if ($type === 'select') {
            echo '<select class="input" id="' . gms_e($id) . '" name="' . gms_e($name) . '"' . $attrs . '>';
            if (!empty($o['placeholder'])) {
                echo '<option value="">' . gms_e($o['placeholder']) . '</option>';
            }
            $opts = $o['options'] ?? [];
            $isList = array_keys($opts) === range(0, count($opts) - 1);
            foreach ($opts as $k => $v) {
                $val = $isList ? $v : $k;
                echo '<option value="' . gms_e($val) . '">' . gms_e($v) . '</option>';
            }
            echo '</select>';
        } elseif ($type === 'textarea') {
            echo '<textarea class="input textarea" id="' . gms_e($id) . '" name="' . gms_e($name) . '" rows="' . (int) ($o['rows'] ?? 3) . '"' . $ph . $attrs . '></textarea>';
        } else {
            echo '<input class="input" type="' . gms_e($type) . '" id="' . gms_e($id) . '" name="' . gms_e($name) . '"' . $ph . $attrs . '>';
        }
        if (!empty($o['hint'])) {
            echo '<p class="mt-1 text-xs text-slate-500">' . gms_e($o['hint']) . '</p>';
        }
        echo '</div>';
    }
}