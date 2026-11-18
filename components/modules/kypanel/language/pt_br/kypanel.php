<?php
$lang['Kypanel.name'] = 'KyPanel';
$lang['Kypanel.description'] = 'Venda e gerencie contas de hospedagem em servidores KyPanel.';
$lang['Kypanel.module_row'] = 'Servidor';
$lang['Kypanel.module_row_plural'] = 'Servidores';
$lang['Kypanel.module_group'] = 'Grupo de servidores';
$lang['Kypanel.back_to_manage'] = 'Voltar';

$lang['Kypanel.order_options.roundrobin'] = 'Distribuir por igual';
$lang['Kypanel.order_options.first'] = 'Primeiro servidor com espaço';

// manage
$lang['Kypanel.add_module_row'] = 'Adicionar servidor';
$lang['Kypanel.add_module_group'] = 'Adicionar grupo';
$lang['Kypanel.manage.tab_rows'] = 'Servidores';
$lang['Kypanel.manage.tab_groups'] = 'Grupos de servidores';
$lang['Kypanel.manage.rows.name'] = 'Nome do servidor';
$lang['Kypanel.manage.rows.host'] = 'Hostname';
$lang['Kypanel.manage.rows.empty'] = 'Nenhum servidor cadastrado.';
$lang['Kypanel.manage.rows.confirm_delete'] = 'Excluir este servidor? As contas nele não são alteradas.';
$lang['Kypanel.manage.groups.name'] = 'Nome do grupo';
$lang['Kypanel.manage.groups.servers'] = 'Servidores';
$lang['Kypanel.manage.groups.empty'] = 'Nenhum grupo cadastrado.';
$lang['Kypanel.manage.groups.confirm_delete'] = 'Excluir este grupo de servidores?';
$lang['Kypanel.manage.options'] = 'Opções';
$lang['Kypanel.manage.edit'] = 'Editar';
$lang['Kypanel.manage.delete'] = 'Excluir';

// server form
$lang['Kypanel.add_row.title'] = 'Adicionar servidor KyPanel';
$lang['Kypanel.add_row.save'] = 'Adicionar servidor';
$lang['Kypanel.edit_row.title'] = 'Editar servidor KyPanel';
$lang['Kypanel.edit_row.save'] = 'Salvar servidor';
$lang['Kypanel.row_meta.server_name'] = 'Nome do servidor';
$lang['Kypanel.row_meta.host_name'] = 'Hostname';
$lang['Kypanel.row_meta.port'] = 'Porta';
$lang['Kypanel.row_meta.api_token'] = 'Chave de API';
$lang['Kypanel.row_meta.api_token_help'] = 'Ky Admin › Development › Manage API Tokens, com "accounts" (leitura e alteração) e "packages" (leitura).';
$lang['Kypanel.row_meta.verify_ssl'] = 'Verificar o certificado SSL (desmarque só em servidor novo, ainda com certificado autoassinado)';

// package
$lang['Kypanel.package_fields.package'] = 'Pacote do KyPanel';
$lang['Kypanel.package_fields.tooltip.package'] = 'Nome do pacote em Ky Admin › Packages.';
$lang['Kypanel.package_fields.reseller'] = 'Criar a conta como revenda';

// service
$lang['Kypanel.service_field.domain'] = 'Domínio';
$lang['Kypanel.service_field.username'] = 'Usuário';
$lang['Kypanel.service_field.password'] = 'Senha';
$lang['Kypanel.service_field.tooltip.username'] = 'Deixe vazio para gerar a partir do domínio.';
$lang['Kypanel.service_field.tooltip.password'] = 'Deixe vazio para gerar uma senha.';
$lang['Kypanel.service_field.tooltip.password_edit'] = 'Deixe vazio para manter a senha atual.';
$lang['Kypanel.suspend_reason'] = 'Suspensa pelo financeiro';

$lang['Kypanel.service_info.domain'] = 'Domínio';
$lang['Kypanel.service_info.username'] = 'Usuário';
$lang['Kypanel.service_info.server'] = 'Servidor';
$lang['Kypanel.service_info.options'] = 'Opções';
$lang['Kypanel.service_info.login'] = 'Entrar no KyPanel';
$lang['Kypanel.tab_login'] = 'Entrar no KyPanel';
$lang['Kypanel.login_failed'] = 'Não foi possível abrir o KyPanel. Tente novamente em instantes.';

// errors
$lang['Kypanel.!error.server_name.empty'] = 'Informe o nome do servidor.';
$lang['Kypanel.!error.host_name.valid'] = 'Informe um hostname ou endereço IP válido.';
$lang['Kypanel.!error.port.valid'] = 'Informe uma porta válida.';
$lang['Kypanel.!error.api_token.empty'] = 'Informe a chave de API.';
$lang['Kypanel.!error.api_token.connection'] = 'Não foi possível conectar ao KyPanel com este hostname, porta e chave.';
$lang['Kypanel.!error.meta[package].empty'] = 'Informe o nome do pacote do KyPanel.';
$lang['Kypanel.!error.module_row.missing'] = 'Nenhum servidor KyPanel para este serviço.';
$lang['Kypanel.!error.kypanel_domain.format'] = 'Informe um domínio válido.';
$lang['Kypanel.!error.kypanel_username.format'] = 'O usuário deve começar com letra e ter só letras minúsculas e números, até 16.';
$lang['Kypanel.!error.kypanel_password.valid'] = 'A senha deve ter pelo menos 8 caracteres.';
