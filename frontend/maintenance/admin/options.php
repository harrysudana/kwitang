<div class="control-group">
    <label class="control-label">Title</label>
    <div class="controls">
        <?php
        echo form_input('maintenance[title]', kconfig('maintenance', 'title', 'Website Offline'), 'id="title" data-placement="left" class="tips input-xxlarge" title="title"');
        ?>
    </div>
</div>

<div class="control-group">
    <label class="control-label">Message</label>
    <div class="controls">
        <?php
        echo form_textarea('maintenance[body]', kconfig('maintenance', 'body', 'We are still on maintenance, please come back later'), 'class="editor"');
        ?>
    </div>
</div>
