<?php

if (!defined('ABSPATH')) die;

?>

<div class="wrap">
  <h1><?= $this->__('InfusionSoft Settings') ?></h1>

  <?php if (!empty($this->error)) : ?>

    <div class="error">
      <p><?= $this->error ?></p>
    </div>

  <?php elseif (!empty($this->message)) : ?>

    <div id="message" class="updated notice notice-success is-dismissible">
      <p>
        <?= $this->message ?>
        <button type="button" class="notice-dismiss">
          <span class="screen-reader-text"><?= $this->__('Dismiss this notice.') ?></span>
        </button>
      </p>
    </div>

  <?php endif; ?>

  <?php if (!empty($data['authorized'])) : ?>

    <h3 class="infusioncrafting-authorized-message">
      <?= $this->__('Your site is authorized. Everything looks good.') ?>
    </h3>

  <?php else: ?>

    <p>
      <a href="<?= $data['auth_url'] ?>"
         class="button button-primary"
       ><?= $this->__('Authorize') ?></a>
    </p>
    <hr>

  <?php endif; ?>

  <form method="post">
    <div class="infusioncrafting-field">
      <label for="client-id-field"><?= $this->__('Client ID') ?></label>
      <input name="client_id"
        id="client-id-field"
        value="<?= esc_attr__($data['client_id']) ?>">
    </div>
    <div class="infusioncrafting-field">
      <label for="client-secret-field"><?= $this->__('Client Secret') ?></label>
      <input name="client_secret"
        id="client-secret-field"
        type="password"
        value="<?= esc_attr__($data['client_secret']) ?>">
    </div>

    <input name="action"
      type="hidden"
      value="update_infusioncrafting_settings">

    <p>
      <button type="submit"
        class="button button-primary"
      ><?= $this->__('Save Settings') ?></button>

      &nbsp;&nbsp;
      <button
        type="submit"
        class="button"
        name="action"
        value="revoke_access_token"
       ><?= $this->__('Revoke Access Token') ?></button>
    </p>
	
	<div class="infusioncraftingTokenUpdateTime">
		<span>
			Last Token Updated on <strong><?php echo esc_attr__($data['TokenUpdateTime']); ?></strong>
		</span>
	</div>
</form>
</div>

<style>
.infusioncrafting-field {
  display: flex;
  max-width: 50em;
}

.infusioncrafting-field label {
  width: 30%;
  font-weight: bold;
}

.infusioncrafting-field input {
  width: 70%;
}

.infusioncrafting-authorized-message:before {
  content: "✔";
  display: inline-block;
  width: 14px;
  height: 14px;
  line-height: 0.8;
  margin: 0;
  padding: 0;
  border: 2px solid green;
  border-radius: 10px;
  color: green;
}

</style>
