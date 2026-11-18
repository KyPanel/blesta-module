<?php

use Blesta\Core\Util\Validate\Server;

// KyPanel provisioning module for Blesta
class Kypanel extends Module
{
    public function __construct()
    {
        Loader::loadComponents($this, ['Input']);
        Language::loadLang('kypanel', null, dirname(__FILE__) . DS . 'language' . DS);
        $this->loadConfig(dirname(__FILE__) . DS . 'config.json');
    }

    public function getGroupOrderOptions()
    {
        return [
            'roundrobin' => Language::_('Kypanel.order_options.roundrobin', true),
            'first' => Language::_('Kypanel.order_options.first', true),
        ];
    }

    // ---- packages ----

    public function getPackageFields($vars = null)
    {
        Loader::loadHelpers($this, ['Html']);
        $fields = new ModuleFields();

        $package = $fields->label(Language::_('Kypanel.package_fields.package', true), 'kypanel_package');
        $package->attach(
            $fields->fieldText('meta[package]', ($vars->meta['package'] ?? null), ['id' => 'kypanel_package'])
        );
        $package->attach($fields->tooltip(Language::_('Kypanel.package_fields.tooltip.package', true)));
        $fields->setField($package);

        $reseller = $fields->label(Language::_('Kypanel.package_fields.reseller', true), 'kypanel_reseller');
        $reseller->attach(
            $fields->fieldCheckbox(
                'meta[reseller]',
                'true',
                ($vars->meta['reseller'] ?? 'false') == 'true',
                ['id' => 'kypanel_reseller']
            )
        );
        $fields->setField($reseller);

        return $fields;
    }

    public function addPackage(array $vars = null)
    {
        return $this->packageMeta($vars);
    }

    public function editPackage($package, array $vars = null)
    {
        return $this->packageMeta($vars);
    }

    private function packageMeta(array $vars = null)
    {
        if (empty($vars['meta']['reseller'])) {
            $vars['meta']['reseller'] = 'false';
        }
        $this->Input->setRules([
            'meta[package]' => [
                'empty' => [
                    'rule' => 'isEmpty',
                    'negate' => true,
                    'message' => Language::_('Kypanel.!error.meta[package].empty', true),
                ],
            ],
        ]);

        $meta = [];
        if ($this->Input->validates($vars)) {
            foreach (['package', 'reseller'] as $key) {
                $meta[] = ['key' => $key, 'value' => $vars['meta'][$key], 'encrypted' => 0];
            }
        }
        return $meta;
    }

    // ---- servers ----

    public function manageModule($module, array &$vars)
    {
        $this->view = $this->makeView('manage');
        Loader::loadHelpers($this, ['Form', 'Html', 'Widget']);
        $this->view->set('module', $module);
        return $this->view->fetch();
    }

    public function manageAddRow(array &$vars)
    {
        $this->view = $this->makeView('add_row');
        Loader::loadHelpers($this, ['Form', 'Html', 'Widget']);
        if (!empty($vars) && empty($vars['verify_ssl'])) {
            $vars['verify_ssl'] = 'false';
        }
        $this->view->set('module', $this->thisModule());
        $this->view->set('vars', (object) $vars);
        return $this->view->fetch();
    }

    public function manageEditRow($module_row, array &$vars)
    {
        $this->view = $this->makeView('edit_row');
        Loader::loadHelpers($this, ['Form', 'Html', 'Widget']);
        if (empty($vars)) {
            $vars = $module_row->meta;
        } elseif (empty($vars['verify_ssl'])) {
            $vars['verify_ssl'] = 'false';
        }
        $this->view->set('module', $this->thisModule());
        $this->view->set('vars', (object) $vars);
        return $this->view->fetch();
    }

    public function addModuleRow(array &$vars)
    {
        return $this->rowMeta($vars);
    }

    public function editModuleRow($module_row, array &$vars)
    {
        return $this->rowMeta($vars);
    }

    private function rowMeta(array &$vars)
    {
        $keys = ['server_name', 'host_name', 'port', 'api_token', 'verify_ssl'];
        if (empty($vars['verify_ssl'])) {
            $vars['verify_ssl'] = 'false';
        }
        if (empty($vars['port'])) {
            $vars['port'] = '7711';
        }

        $this->Input->setRules($this->getRowRules($vars));
        if (!$this->Input->validates($vars)) {
            return;
        }

        $meta = [];
        foreach ($keys as $key) {
            $meta[] = [
                'key' => $key,
                'value' => trim((string) ($vars[$key] ?? '')),
                'encrypted' => $key == 'api_token' ? 1 : 0,
            ];
        }
        return $meta;
    }

    private function getRowRules(array &$vars)
    {
        return [
            'server_name' => [
                'empty' => [
                    'rule' => 'isEmpty',
                    'negate' => true,
                    'message' => Language::_('Kypanel.!error.server_name.empty', true),
                ],
            ],
            'host_name' => [
                'valid' => [
                    'rule' => [[$this, 'validateHostName']],
                    'message' => Language::_('Kypanel.!error.host_name.valid', true),
                ],
            ],
            'port' => [
                'valid' => [
                    'rule' => ['matches', '/^[0-9]{1,5}$/'],
                    'message' => Language::_('Kypanel.!error.port.valid', true),
                ],
            ],
            'api_token' => [
                'empty' => [
                    'last' => true,
                    'rule' => 'isEmpty',
                    'negate' => true,
                    'message' => Language::_('Kypanel.!error.api_token.empty', true),
                ],
                'connection' => [
                    'rule' => [
                        [$this, 'validateConnection'],
                        $vars['host_name'] ?? '',
                        $vars['port'] ?? '7711',
                        $vars['verify_ssl'] ?? 'false',
                    ],
                    'message' => Language::_('Kypanel.!error.api_token.connection', true),
                ],
            ],
        ];
    }

    public function validateHostName($host_name)
    {
        $validator = new Server();
        return $validator->isDomain($host_name) || $validator->isIp($host_name);
    }

    public function validateConnection($api_token, $host_name, $port, $verify_ssl)
    {
        Loader::load(dirname(__FILE__) . DS . 'apis' . DS . 'kypanel_api.php');
        try {
            $api = new KypanelApi($host_name, $api_token, $port, $verify_ssl == 'true');
            $api->testConnection();
            $this->log($host_name . '|test', $api->lastRequest, 'input', true);
            $this->log($host_name . '|test', $api->lastResponse, 'output', true);
            return true;
        } catch (\Throwable $e) {
            $this->log($host_name . '|test', $e->getMessage(), 'output', false);
            return false;
        }
    }

    // ---- services ----

    public function getServiceName($service)
    {
        foreach ($service->fields as $field) {
            if ($field->key == 'kypanel_domain') {
                return $field->value;
            }
        }
        return null;
    }

    public function getPackageServiceName($package, array $vars = null)
    {
        return $vars['kypanel_domain'] ?? null;
    }

    public function getAdminAddFields($package, $vars = null)
    {
        return $this->serviceFields($vars, true, false);
    }

    public function getClientAddFields($package, $vars = null)
    {
        Loader::loadHelpers($this, ['Html']);
        $fields = new ModuleFields();
        $domain = $fields->label(Language::_('Kypanel.service_field.domain', true), 'kypanel_domain');
        $domain->attach(
            $fields->fieldText(
                'kypanel_domain',
                ($vars->kypanel_domain ?? ($vars->domain ?? null)),
                ['id' => 'kypanel_domain']
            )
        );
        $fields->setField($domain);
        return $fields;
    }

    public function getAdminEditFields($package, $vars = null)
    {
        return $this->serviceFields($vars, false, true);
    }

    private function serviceFields($vars, $tooltips, $edit)
    {
        Loader::loadHelpers($this, ['Html']);
        $fields = new ModuleFields();

        $domain = $fields->label(Language::_('Kypanel.service_field.domain', true), 'kypanel_domain');
        $domain->attach($fields->fieldText('kypanel_domain', ($vars->kypanel_domain ?? null), ['id' => 'kypanel_domain']));
        $fields->setField($domain);

        $username = $fields->label(Language::_('Kypanel.service_field.username', true), 'kypanel_username');
        $username->attach($fields->fieldText('kypanel_username', ($vars->kypanel_username ?? null), ['id' => 'kypanel_username']));
        if ($tooltips) {
            $username->attach($fields->tooltip(Language::_('Kypanel.service_field.tooltip.username', true)));
        }
        $fields->setField($username);

        $password = $fields->label(Language::_('Kypanel.service_field.password', true), 'kypanel_password');
        $password->attach(
            $fields->fieldPassword('kypanel_password', ['id' => 'kypanel_password', 'value' => ($edit ? null : ($vars->kypanel_password ?? null))])
        );
        $password->attach($fields->tooltip(Language::_(
            $edit ? 'Kypanel.service_field.tooltip.password_edit' : 'Kypanel.service_field.tooltip.password',
            true
        )));
        $fields->setField($password);

        return $fields;
    }

    public function validateService($package, array $vars = null)
    {
        $this->Input->setRules($this->getServiceRules($vars));
        return $this->Input->validates($vars);
    }

    public function validateServiceEdit($service, array $vars = null)
    {
        $this->Input->setRules($this->getServiceRules($vars, true));
        return $this->Input->validates($vars);
    }

    private function getServiceRules(array $vars = null, $edit = false)
    {
        $rules = [
            'kypanel_domain' => [
                'format' => [
                    'if_set' => $edit,
                    'rule' => [[$this, 'validateHostName']],
                    'message' => Language::_('Kypanel.!error.kypanel_domain.format', true),
                ],
            ],
            'kypanel_username' => [
                'format' => [
                    'if_set' => true,
                    'rule' => ['matches', '/^[a-z][a-z0-9]{0,15}$/'],
                    'message' => Language::_('Kypanel.!error.kypanel_username.format', true),
                ],
            ],
            'kypanel_password' => [
                'valid' => [
                    'if_set' => true,
                    'rule' => ['isPassword', 8],
                    'message' => Language::_('Kypanel.!error.kypanel_password.valid', true),
                ],
            ],
        ];
        foreach (['kypanel_username', 'kypanel_password'] as $key) {
            if (empty($vars[$key])) {
                unset($rules[$key]);
            }
        }
        return $rules;
    }

    public function addService($package, array $vars = null, $parent_package = null, $parent_service = null, $status = 'pending')
    {
        $row = $this->getModuleRow();
        if (!$row) {
            $this->Input->setErrors(['module_row' => ['missing' => Language::_('Kypanel.!error.module_row.missing', true)]]);
            return;
        }

        if (isset($vars['kypanel_domain'])) {
            $vars['kypanel_domain'] = $this->formatDomain($vars['kypanel_domain']);
        }
        $this->validateService($package, $vars);
        if ($this->Input->errors()) {
            return;
        }

        if (($vars['use_module'] ?? 'false') == 'true') {
            $email = '';
            if (!empty($vars['client_id'])) {
                Loader::loadModels($this, ['Clients']);
                $client = $this->Clients->get($vars['client_id'], false);
                $email = $client ? $client->email : '';
            }
            $ok = $this->call($row, 'create', function (KypanelApi $api) use (&$vars, $package, $email) {
                if (empty($vars['kypanel_username'])) {
                    $vars['kypanel_username'] = $this->generateUsername($api, $vars['kypanel_domain']);
                }
                if (empty($vars['kypanel_password'])) {
                    $vars['kypanel_password'] = $this->generatePassword();
                }
                $api->createAccount(
                    $vars['kypanel_username'],
                    $vars['kypanel_domain'],
                    $vars['kypanel_password'],
                    $email,
                    $package->meta->package,
                    ($package->meta->reseller ?? 'false') == 'true'
                );
            });
            if (!$ok) {
                return;
            }
        }

        return [
            ['key' => 'kypanel_domain', 'value' => $vars['kypanel_domain'], 'encrypted' => 0],
            ['key' => 'kypanel_username', 'value' => $vars['kypanel_username'] ?? '', 'encrypted' => 0],
            ['key' => 'kypanel_password', 'value' => $vars['kypanel_password'] ?? '', 'encrypted' => 1],
        ];
    }

    public function editService($package, $service, array $vars = null, $parent_package = null, $parent_service = null)
    {
        $row = $this->getModuleRow();
        if (isset($vars['kypanel_domain'])) {
            $vars['kypanel_domain'] = $this->formatDomain($vars['kypanel_domain']);
        }
        $this->validateServiceEdit($service, $vars);
        if ($this->Input->errors()) {
            return;
        }

        $fields = $this->serviceFieldsToObject($service->fields);
        if (isset($vars['kypanel_password']) && $vars['kypanel_password'] === '') {
            unset($vars['kypanel_password']);
        }

        if ($row && ($vars['use_module'] ?? 'false') == 'true'
            && isset($vars['kypanel_password']) && $vars['kypanel_password'] !== ($fields->kypanel_password ?? null)) {
            $username = $fields->kypanel_username ?? '';
            $password = $vars['kypanel_password'];
            if (!$this->call($row, 'password', function (KypanelApi $api) use ($username, $password) {
                $api->changePassword($username, $password);
            })) {
                return;
            }
        }

        foreach (['kypanel_domain', 'kypanel_username', 'kypanel_password'] as $key) {
            if (isset($vars[$key])) {
                $fields->$key = $vars[$key];
            }
        }

        $out = [];
        foreach ($fields as $key => $value) {
            $out[] = ['key' => $key, 'value' => $value, 'encrypted' => $key == 'kypanel_password' ? 1 : 0];
        }
        return $out;
    }

    public function suspendService($package, $service, $parent_package = null, $parent_service = null)
    {
        $username = $this->serviceUsername($service);
        $reason = Language::_('Kypanel.suspend_reason', true);
        $this->onService('suspend', function (KypanelApi $api) use ($username, $reason) {
            $api->suspend($username, $reason);
        });
        return null;
    }

    public function unsuspendService($package, $service, $parent_package = null, $parent_service = null)
    {
        $username = $this->serviceUsername($service);
        $this->onService('unsuspend', function (KypanelApi $api) use ($username) {
            $api->unsuspend($username);
        });
        return null;
    }

    public function cancelService($package, $service, $parent_package = null, $parent_service = null)
    {
        $username = $this->serviceUsername($service);
        $this->onService('terminate', function (KypanelApi $api) use ($username) {
            $api->terminate($username);
        });
        return null;
    }

    public function changeServicePackage($package_from, $package_to, $service, $parent_package = null, $parent_service = null)
    {
        $username = $this->serviceUsername($service);
        $to = $package_to->meta->package ?? '';
        $this->onService('package', function (KypanelApi $api) use ($username, $to) {
            $api->changePackage($username, $to);
        });
        return null;
    }

    // ---- service pages ----

    public function getAdminServiceInfo($service, $package)
    {
        return $this->serviceInfo('admin_service_info', $service, $package);
    }

    public function getClientServiceInfo($service, $package)
    {
        return $this->serviceInfo('client_service_info', $service, $package);
    }

    private function serviceInfo($view, $service, $package)
    {
        $this->view = $this->makeView($view);
        Loader::loadHelpers($this, ['Form', 'Html']);
        $this->view->set('module_row', $this->getModuleRow());
        $this->view->set('package', $package);
        $this->view->set('service', $service);
        $this->view->set('service_fields', $this->serviceFieldsToObject($service->fields));
        return $this->view->fetch();
    }

    public function getClientTabs($package)
    {
        return ['tabClientLogin' => Language::_('Kypanel.tab_login', true)];
    }

    public function getAdminTabs($package)
    {
        return ['tabAdminLogin' => Language::_('Kypanel.tab_login', true)];
    }

    // the sign-in link lasts 60 seconds, so it's made on click and not when the page loads
    public function tabClientLogin($package, $service, array $get = null, array $post = null, array $files = null)
    {
        return $this->loginRedirect($service);
    }

    public function tabAdminLogin($package, $service, array $get = null, array $post = null, array $files = null)
    {
        return $this->loginRedirect($service);
    }

    private function loginRedirect($service)
    {
        $row = $this->getModuleRow($service->module_row_id);
        $username = $this->serviceUsername($service);
        $url = '';
        if ($row) {
            $this->call($row, 'sso', function (KypanelApi $api) use ($username, &$url) {
                $url = $api->loginUrl($username);
            });
        }
        if ($url !== '') {
            header('Location: ' . $url);
            exit;
        }
        $this->view = $this->makeView('login_failed');
        Loader::loadHelpers($this, ['Html']);
        $errors = $this->Input->errors();
        $this->view->set('error', $errors ? current(current($errors)) : '');
        return $this->view->fetch();
    }

    public function getEmailTags()
    {
        return [
            'module' => ['host_name'],
            'package' => ['package'],
            'service' => ['kypanel_domain', 'kypanel_username', 'kypanel_password'],
        ];
    }

    // ---- helpers ----

    private function makeView($name)
    {
        $view = new View($name, 'default');
        $view->base_uri = $this->base_uri;
        $view->setDefaultView('components' . DS . 'modules' . DS . 'kypanel' . DS);
        return $view;
    }

    private function thisModule()
    {
        Loader::loadModels($this, ['ModuleManager']);
        $module = $this->ModuleManager->getByClass('kypanel', Configure::get('Blesta.company_id'));
        return (object) ($module[0] ?? []);
    }

    private function api($row)
    {
        Loader::load(dirname(__FILE__) . DS . 'apis' . DS . 'kypanel_api.php');
        return new KypanelApi(
            $row->meta->host_name,
            $row->meta->api_token,
            $row->meta->port ?? 7711,
            ($row->meta->verify_ssl ?? 'false') == 'true'
        );
    }

    // runs $fn with an API client, logs it and turns failures into Input errors
    private function call($row, $action, callable $fn)
    {
        $api = null;
        try {
            $api = $this->api($row);
            $fn($api);
            $this->log($row->meta->host_name . '|' . $action, $api->lastRequest, 'input', true);
            $this->log($row->meta->host_name . '|' . $action, $api->lastResponse, 'output', true);
            return true;
        } catch (\Throwable $e) {
            $this->log($row->meta->host_name . '|' . $action, $api ? $api->lastRequest : '', 'input', true);
            $this->log($row->meta->host_name . '|' . $action, $e->getMessage(), 'output', false);
            $this->Input->setErrors(['api' => ['response' => $e->getMessage()]]);
            return false;
        }
    }

    private function onService($action, callable $fn)
    {
        $row = $this->getModuleRow();
        if (!$row) {
            $this->Input->setErrors(['module_row' => ['missing' => Language::_('Kypanel.!error.module_row.missing', true)]]);
            return false;
        }
        return $this->call($row, $action, $fn);
    }

    private function serviceUsername($service)
    {
        $fields = $this->serviceFieldsToObject($service->fields);
        return $fields->kypanel_username ?? '';
    }

    private function formatDomain($domain)
    {
        return strtolower(preg_replace('/^\s*www\./i', '', trim((string) $domain)));
    }

    // KyPanel usernames: a letter, then letters or digits, 16 max
    private function generateUsername(KypanelApi $api, $domain)
    {
        $base = ltrim(preg_replace('/[^a-z0-9]/', '', strtolower($domain)), '0123456789');
        if ($base === '') {
            $base = 'user';
        }
        $base = substr($base, 0, 8);
        if (!$api->accountExists($base)) {
            return $base;
        }
        for ($i = 1; $i < 1000; $i++) {
            $name = substr($base, 0, 8 - strlen((string) $i)) . $i;
            if (!$api->accountExists($name)) {
                return $name;
            }
        }
        throw new RuntimeException('Couldn\'t find a free username for ' . $domain . '.');
    }

    private function generatePassword($length = 16)
    {
        $pool = 'abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $out = '';
        for ($i = 0; $i < $length; $i++) {
            $out .= $pool[random_int(0, strlen($pool) - 1)];
        }
        return $out;
    }
}
