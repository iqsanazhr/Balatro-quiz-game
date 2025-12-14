<div class="hud-top">
    <div class="hud-item user-info">
        <span class="label"><?php echo isset($page_label) ? $page_label : 'PAGE'; ?></span>
    </div>
    <?php if (isset($show_chips) && $show_chips && isset($current_chips)): ?>
        <div class="hud-item chips-display">
            <span class="label">CHIPS:</span> <span
                class="value text-gold">$<?php echo number_format($current_chips); ?></span>
        </div>
    <?php elseif (isset($show_count) && $show_count && isset($count_value)): ?>
        <div class="hud-item chips-display">
            <span class="label">COUNT:</span> <span class="value text-gold"><?php echo $count_value; ?></span>
        </div>
    <?php endif; ?>
</div>