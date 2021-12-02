<?php

use MavenAlgolia\Admin\Controllers\Settings;
use MavenAlgolia\Core;

$registry = Core\Registry::instance();
$langDomain = $registry->getPluginShortName();
?>
<div id="mvnAlgoliaSettings" class="wrap">
	<div id="icon-options-general" class="icon32"><br></div>
	<h2>Algolia Settings</h2>
	<form action="" method="post">
		<div id="mvnAlgtabs">
			<ul>
				<li><a class="nav-tab nav-tab-active" href="#tab-general"><?php esc_html_e('Account', $langDomain); ?></a></li>
			</ul>
			<div id="tab-general">
				<input type="hidden" value="<?php echo Settings::updateAction; ?>" name="mvnAlg_action">
				<?php wp_nonce_field(Settings::updateAction); ?>
				<table class="widefat">
					<thead>
						<tr>
							<th class="row-title" colspan="2"><strong><?php esc_html_e('Configure your App Credentials', $langDomain); ?></strong></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<th scope="row"><label for="mvnAlg_appId"><?php esc_html_e('APP ID', $langDomain); ?></label></th>
							<td><input type="text" class="regular-text" value="<?php echo esc_attr($registry->getAppId()); ?>" id="mvnAlg_appId" name="<?php echo Settings::settingsField; ?>[appId]"></td>
						</tr>
						<tr>
							<th scope="row"><label for="mvnAlg_apiKey"><?php esc_html_e('API Key', $langDomain); ?></label></th>
							<td><input type="text" class="regular-text" value="<?php echo esc_attr($registry->getApiKey()); ?>" id="mvnAlg_apiKey" name="<?php echo Settings::settingsField; ?>[apiKey]"></td>
						</tr>
						<tr>
							<th scope="row"><label for="mvnAlg_apiKeySearch"><?php esc_html_e('API Key for Search Only', $langDomain); ?></label></th>
							<td><input type="text" class="regular-text" value="<?php echo esc_attr($registry->getApiKeySearch()); ?>" id="mvnAlg_apiKeySearch" name="<?php echo Settings::settingsField; ?>[apiKeySearch]"></td>
						</tr>
						<tr>
							<th scope="row"><label for="mvnAlg_apiKeySearch"><?php esc_html_e('Index', $langDomain); ?></label></th>
							<td>
								<?php if ( strpos($_SERVER['HTTP_HOST'], PRODSITE) !== false ) { ?>
									<?php echo PRODINDEX; ?> <input type="hidden" class="regular-text" value="<?php echo PRODINDEX; ?>" id="mvnAlg_defaultIndex" name="<?php echo Settings::settingsField; ?>[defaultIndex]">
								<?php } else if ( strpos($_SERVER['HTTP_HOST'], STAGSITE) !== false ) { ?>
									<?php echo STAGINDEX; ?> <input type="hidden" class="regular-text" value="<?php echo STAGINDEX; ?>" id="mvnAlg_defaultIndex" name="<?php echo Settings::settingsField; ?>[defaultIndex]">
								<?php } else { ?>
									<?php echo DEVINDEX; ?> <input type="hidden" class="regular-text" value="<?php echo DEVINDEX; ?>" id="mvnAlg_defaultIndex" name="<?php echo Settings::settingsField; ?>[defaultIndex]">
								<?php } ?>
							</td>
						</tr>
						<tr>
							<td>
								<p class="submit"><input type="submit" value="<?php esc_attr_e('Save Changes', $langDomain); ?>" class="button button-primary" id="submit" name="submit"></p>
							</td>
						</tr>
					</tbody>
				</table>

				<?php if (Core\UtilsAlgolia::readyToIndex()): ?>
					<?php if ($registry->getDefaultIndex()) { ?>
					<div class="index-action-row index-action-button">
						<div class="algolia-action-button" style="margin-top:20px;margin-bottom:10px;">
							<button type="button" class="button button-secondary" id="mvnAlg_index" name="mvnAlg_index"><?php esc_html_e('Index Content', $langDomain); ?></button>
							<span class="spinner algolia-index-spinner"></span>
						</div>
					</div>
					<div class="index-action-row index-messages">
						<div class="success"><ul id="mvn-alg-index-result"></ul></div>
						<div class="error error-message" style="display: none;"><p id="mvn-alg-index-error" ></p></div>
					</div>
					<?php } ?>
				<?php endif; ?>
			</div>
		</div>
	</form>
</div>