<?php
$lang['Kypanel.name'] = 'KyPanel';
$lang['Kypanel.description'] = 'Sell and manage hosting accounts on KyPanel servers.';
$lang['Kypanel.module_row'] = 'Server';
$lang['Kypanel.module_row_plural'] = 'Servers';
$lang['Kypanel.module_group'] = 'Server Group';
$lang['Kypanel.back_to_manage'] = 'Back';

$lang['Kypanel.order_options.roundrobin'] = 'Evenly distribute';
$lang['Kypanel.order_options.first'] = 'First non-full server';

// manage
$lang['Kypanel.add_module_row'] = 'Add Server';
$lang['Kypanel.add_module_group'] = 'Add Server Group';
$lang['Kypanel.manage.tab_rows'] = 'Servers';
$lang['Kypanel.manage.tab_groups'] = 'Server Groups';
$lang['Kypanel.manage.rows.name'] = 'Server Label';
$lang['Kypanel.manage.rows.host'] = 'Hostname';
$lang['Kypanel.manage.rows.empty'] = 'No servers yet.';
$lang['Kypanel.manage.rows.confirm_delete'] = 'Delete this server? Accounts on it are not touched.';
$lang['Kypanel.manage.groups.name'] = 'Group Name';
$lang['Kypanel.manage.groups.servers'] = 'Servers';
$lang['Kypanel.manage.groups.empty'] = 'No server groups yet.';
$lang['Kypanel.manage.groups.confirm_delete'] = 'Delete this server group?';
$lang['Kypanel.manage.options'] = 'Options';
$lang['Kypanel.manage.edit'] = 'Edit';
$lang['Kypanel.manage.delete'] = 'Delete';

// server form
$lang['Kypanel.add_row.title'] = 'Add KyPanel Server';
$lang['Kypanel.add_row.save'] = 'Add Server';
$lang['Kypanel.edit_row.title'] = 'Edit KyPanel Server';
$lang['Kypanel.edit_row.save'] = 'Update Server';
$lang['Kypanel.row_meta.server_name'] = 'Server Label';
$lang['Kypanel.row_meta.host_name'] = 'Hostname';
$lang['Kypanel.row_meta.port'] = 'Port';
$lang['Kypanel.row_meta.api_token'] = 'API Token';
$lang['Kypanel.row_meta.api_token_help'] = 'Ky Admin › Development › Manage API Tokens, with "accounts" (read and change) and "packages" (read).';
$lang['Kypanel.row_meta.verify_ssl'] = 'Verify the SSL certificate (untick only for a new server that still has a self-signed one)';

// package
$lang['Kypanel.package_fields.package'] = 'KyPanel Package';
$lang['Kypanel.package_fields.tooltip.package'] = 'Name of the package in Ky Admin › Packages.';
$lang['Kypanel.package_fields.reseller'] = 'Create the account as a reseller';

// service
$lang['Kypanel.service_field.domain'] = 'Domain';
$lang['Kypanel.service_field.username'] = 'Username';
$lang['Kypanel.service_field.password'] = 'Password';
$lang['Kypanel.service_field.tooltip.username'] = 'Leave empty to make one from the domain.';
$lang['Kypanel.service_field.tooltip.password'] = 'Leave empty to generate one.';
$lang['Kypanel.service_field.tooltip.password_edit'] = 'Leave empty to keep the current password.';
$lang['Kypanel.suspend_reason'] = 'Suspended by billing';

$lang['Kypanel.service_info.domain'] = 'Domain';
$lang['Kypanel.service_info.username'] = 'Username';
$lang['Kypanel.service_info.server'] = 'Server';
$lang['Kypanel.service_info.options'] = 'Options';
$lang['Kypanel.service_info.login'] = 'Log in to KyPanel';
$lang['Kypanel.tab_login'] = 'Log in to KyPanel';
$lang['Kypanel.login_failed'] = 'Couldn\'t open KyPanel. Try again in a moment.';

// errors
$lang['Kypanel.!error.server_name.empty'] = 'Enter a server label.';
$lang['Kypanel.!error.host_name.valid'] = 'Enter a valid hostname or IP address.';
$lang['Kypanel.!error.port.valid'] = 'Enter a valid port.';
$lang['Kypanel.!error.api_token.empty'] = 'Enter the API token.';
$lang['Kypanel.!error.api_token.connection'] = 'Couldn\'t connect to KyPanel with this hostname, port and token.';
$lang['Kypanel.!error.meta[package].empty'] = 'Enter the KyPanel package name.';
$lang['Kypanel.!error.module_row.missing'] = 'There is no KyPanel server for this service.';
$lang['Kypanel.!error.kypanel_domain.format'] = 'Enter a valid domain.';
$lang['Kypanel.!error.kypanel_username.format'] = 'The username must start with a letter and have only lowercase letters and digits, 16 at most.';
$lang['Kypanel.!error.kypanel_password.valid'] = 'The password must have at least 8 characters.';
