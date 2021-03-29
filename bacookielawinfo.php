<?php
/**
* 2007-2020 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2020 PrestaShop SA
*  @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
* @since 1.6
*/

class Bacookielawinfo extends Module
{
    private $demoMode=false;
    private $languageArray;
    public function __construct()
    {
        $this->name = "bacookielawinfo";
        $this->tab = "others";
        $this->version = "1.0.10";
        $this->author = "buy-addons";
        $this->need_instance = 0;
        $this->secure_key = Tools::encrypt($this->name);
        $this->bootstrap = true;
        $this->module_key = 'dda2c6b9133bcfe2727a81b6245dbae2';
        $this->languageArray = Language::getLanguages(false);
        parent::__construct();
        if (strpos(_PS_VERSION_, "1.5") === 0) {
            $this->smarty=$this->context->smarty;
        }
        $this->displayName = $this->l('BA Cookie Law Info');
        $this->description = $this->l('Prestashop BA Cookie Law Info - buy-addons');
    }
    
    public function install()
    {
        $this->saveDefaultConfig();
        if (parent::install() == false) {
            return false;
        }
        if ($this->registerHook("displayHeader")==false || $this->registerHook("header")==false) {
            return false;
        }
        return true;
    }
    
    public function uninstall()
    {
        if (parent::uninstall()) {
            return true;
        }
        return true;
    }
    
    public function getContent()
    {
        $html=null;
        $token=Tools::getAdminTokenLite('AdminModules');
        $bamodule=AdminController::$currentIndex;
        $buttonSubmitSaveArr = array(
            'submitba',
            'submitBaAndStay'
        );
        if ($this->demoMode==true) {
            foreach ($buttonSubmitSaveArr as $buttonSubmitSave) {
                if (Tools::isSubmit($buttonSubmitSave)) {
                    Tools::redirectAdmin($bamodule.'&token='.$token.'&configure='.$this->name.'&demoMode=1');
                }
            }
        }
        $this->smarty->assign('demoMode', Tools::getValue('demoMode'));
        if (Tools::isSubmit('submitba')) {
            $this->save();
            Tools::redirectAdmin($bamodule.'&token='.$token);
        } elseif (Tools::isSubmit('submitBaAndStay')) {
            $this->save();
            $html.=$this->displayConfirmation($this->l('Update successful'));
        } elseif (Tools::isSubmit('submitbaCancel')) {
            Tools::redirectAdmin($bamodule.'&token='.$token);
        }
        $this->getData();
        $this->context->controller->addJS($this->_path.'views/js/common.js');
        $this->context->controller->addJS($this->_path.'views/js/jscolor/jscolor.js');
        $this->context->controller->addCSS($this->_path.'views/css/style_backend.css');
        if (strpos(_PS_VERSION_, "1.5") === 0) {
            $this->context->controller->addCSS($this->_path.'views/css/style_backend_v1.5.css');
        }
        $iso=$this->context->language->iso_code;
        // load Tiny MCE Editor
        $html .= '
        <script type="text/javascript">    
            var iso = \''.(file_exists(_PS_ROOT_DIR_.'/js/tiny_mce/langs/'.$iso.'.js') ? $iso : 'en').'\' ;
            var pathCSS = \''._THEME_CSS_DIR_.'\' ;
            var ad = \''.dirname($_SERVER['PHP_SELF']).'\' ;
        </script>
        <script type="text/javascript" src="'.__PS_BASE_URI__.'js/tiny_mce/tiny_mce.js"></script>
        <script type="text/javascript" src="'.__PS_BASE_URI__.'js/tinymce.inc.js"></script>
        <script type="text/javascript" src="'.__PS_BASE_URI__.'js/admin/tinymce.inc.js"></script>
        <script language="javascript" type="text/javascript">
            id_language = Number('.$this->context->language->id.');
            tinySetup();
        </script>';
        $html.=$this->display(__FILE__, 'views/templates/admin/form_config.tpl');
        return $html;
    }
    public function saveDefaultConfig()
    {
        Configuration::updateValue('BAEU_CookieBarIsCurrently', 'Y');
        Configuration::updateValue('BAEU_CookieBarShowIn', '2');
        Configuration::updateValue('BAEU_OnLoad', '1');
        Configuration::updateValue('BAEU_OnHide', '2');
        Configuration::updateValue('BAEU_AutoHideCookieAfterDelay', 'N');
        Configuration::updateValue('BAEU_MillisecondUntilHide', '3000');
        Configuration::updateValue('BAEU_AutoHideCookieIfScroll', 'N');
        Configuration::updateValue('BAEU_ReloadAfterAcceptEvent', 'N');
        $BAEU_Message='According to the EU cookie law, you must accept the use all the features of our websites';
        foreach ($this->languageArray as $languageArray) {
            Configuration::updateValue('BAEU_Message_'.$languageArray['id_lang'], $BAEU_Message);
            Configuration::updateValue('BAEU_leftLinkText_'.$languageArray['id_lang'], 'I Accept');
            Configuration::updateValue('BAEU_rightLinkText_'.$languageArray['id_lang'], 'More Information');
        }
        Configuration::updateValue('BAEU_CookieBarColor', 'ffffff');
        Configuration::updateValue('BAEU_TextColor', '000000');
        Configuration::updateValue('BAEU_ShowBorder', 'Y');
        Configuration::updateValue('BAEU_BoderColor', '444444');
        Configuration::updateValue('BAEU_leftAction', '1');
        Configuration::updateValue('BAEU_leftLinkURL', 'index.php');
        Configuration::updateValue('BAEU_leftOpenLinkInNewWindow', 'N');
        Configuration::updateValue('BAEU_leftLinkColor', 'ffffff');
        Configuration::updateValue('BAEU_leftShowAsButton', 'Y');
        Configuration::updateValue('BAEU_leftButtonColor', '333333');
        Configuration::updateValue('BAEU_leftHoverButtonColor', 'cccccc');
        Configuration::updateValue('BAEU_rightLinkURL', 'index.php');
        Configuration::updateValue('BAEU_rightLinkColor', '140805');
        Configuration::updateValue('BAEU_RightOpenLinkInNewWinDown', 'N');
        Configuration::updateValue('BAEU_rightShowAsButton', 'N');
        Configuration::updateValue('BAEU_rightButtonColor', '333333');
        Configuration::updateValue('BAEU_rightHoverButtonColor', 'cccccc');
        Configuration::updateValue('BAEU_customCss', '');
    }
    private function save()
    {
    
        $BAEU_CookieBarIsCurrently = (Tools::getValue('BAEU_CookieBarIsCurrently')!=false)?'Y':'N';
        Configuration::updateValue('BAEU_CookieBarIsCurrently', $BAEU_CookieBarIsCurrently);
        Configuration::updateValue('BAEU_CookieBarShowIn', (int)Tools::getValue('BAEU_CookieBarShowIn'));
        Configuration::updateValue('BAEU_OnLoad', (int)Tools::getValue('BAEU_OnLoad'));
        Configuration::updateValue('BAEU_OnHide', (int)Tools::getValue('BAEU_OnHide'));
        $BAEU_AutoHideCookieAfterDelay = (Tools::getValue('BAEU_AutoHideCookieAfterDelay')!=false)?'Y':'N';
        Configuration::updateValue('BAEU_AutoHideCookieAfterDelay', $BAEU_AutoHideCookieAfterDelay);
        Configuration::updateValue('BAEU_MillisecondUntilHide', (int)Tools::getValue('BAEU_MillisecondUntilHide'));
        $BAEU_AutoHideCookieIfScroll = (Tools::getValue('BAEU_AutoHideCookieIfScroll')!=false)?'Y':'N';
        Configuration::updateValue('BAEU_AutoHideCookieIfScroll', $BAEU_AutoHideCookieIfScroll);
        $BAEU_ReloadAfterAcceptEvent = (Tools::getValue('BAEU_ReloadAfterAcceptEvent')!=false)?'Y':'N';
        Configuration::updateValue('BAEU_ReloadAfterAcceptEvent', $BAEU_ReloadAfterAcceptEvent);
        $idLangDefault = (int) (Configuration::get('PS_LANG_DEFAULT'));
        $isoDefaultLanguage = Language::getIsoById($idLangDefault);
        
        foreach ($this->languageArray as $languageArray) {
            $BAEU_Message = Tools::getValue('BAEU_Message_'.$languageArray['id_lang']);
            $BAEU_leftLinkText = Tools::getValue('BAEU_leftLinkText_'.$languageArray['id_lang']);
            $BAEU_rightLinkText = Tools::getValue('BAEU_rightLinkText_'.$languageArray['id_lang']);
            if ($languageArray['iso_code'] != $isoDefaultLanguage) {
                if (empty($BAEU_Message)) {
                    $BAEU_Message = Tools::htmlentitiesUTF8(Tools::getValue('BAEU_Message_'.$idLangDefault));
                }
                if (empty($BAEU_leftLinkText)) {
                    $BAEU_leftLinkText = Tools::getValue('BAEU_leftLinkText_'.$languageArray['id_lang']);
                }
                if (empty($BAEU_rightLinkText)) {
                    $BAEU_rightLinkText = Tools::getValue('BAEU_rightLinkText_'.$languageArray['id_lang']);
                }
            }
            Configuration::updateValue('BAEU_Message_'.$languageArray['id_lang'], $BAEU_Message);
            Configuration::updateValue('BAEU_leftLinkText_'.$languageArray['id_lang'], $BAEU_leftLinkText);
            Configuration::updateValue('BAEU_rightLinkText_'.$languageArray['id_lang'], $BAEU_rightLinkText);
        }
        Configuration::updateValue('BAEU_CookieBarColor', Tools::getValue('BAEU_CookieBarColor'));
        Configuration::updateValue('BAEU_TextColor', Tools::getValue('BAEU_TextColor'));
        Configuration::updateValue('BAEU_ShowBorder', (Tools::getValue('BAEU_ShowBorder')!=false)?'Y':'N');
        Configuration::updateValue('BAEU_BoderColor', Tools::getValue('BAEU_BoderColor'));
        Configuration::updateValue('BAEU_leftAction', (int)Tools::getValue('BAEU_leftAction'));
        Configuration::updateValue('BAEU_leftLinkURL', Tools::getValue('BAEU_leftLinkURL'));
        $BAEU_leftOpenLinkInNewWindow = (Tools::getValue('BAEU_leftOpenLinkInNewWindow')!=false)?'Y':'N';
        Configuration::updateValue('BAEU_leftOpenLinkInNewWindow', $BAEU_leftOpenLinkInNewWindow);
        Configuration::updateValue('BAEU_leftLinkColor', Tools::getValue('BAEU_leftLinkColor'));
        $BAEU_leftShowAsButton = (Tools::getValue('BAEU_leftShowAsButton')!=false)?'Y':'N';
        Configuration::updateValue('BAEU_leftShowAsButton', $BAEU_leftShowAsButton);
        Configuration::updateValue('BAEU_leftButtonColor', Tools::getValue('BAEU_leftButtonColor'));
        Configuration::updateValue('BAEU_leftHoverButtonColor', Tools::getValue('BAEU_leftHoverButtonColor'));
        Configuration::updateValue('BAEU_rightLinkURL', Tools::getValue('BAEU_rightLinkURL'));
        Configuration::updateValue('BAEU_rightLinkColor', Tools::getValue('BAEU_rightLinkColor'));
        $BAEU_RightOpenLinkInNewWinDown = (Tools::getValue('BAEU_RightOpenLinkInNewWinDown')!=false)?'Y':'N';
        Configuration::updateValue('BAEU_RightOpenLinkInNewWinDown', $BAEU_RightOpenLinkInNewWinDown);
        $BAEU_rightShowAsButton = (Tools::getValue('BAEU_rightShowAsButton')!=false)?'Y':'N';
        Configuration::updateValue('BAEU_rightShowAsButton', $BAEU_rightShowAsButton);
        Configuration::updateValue('BAEU_rightButtonColor', Tools::getValue('BAEU_rightButtonColor'));
        Configuration::updateValue('BAEU_rightHoverButtonColor', Tools::getValue('BAEU_rightHoverButtonColor'));
        Configuration::updateValue('BAEU_customCss', Tools::getValue('BAEU_customCss'));
    }
    private function getData()
    {
        $this->smarty->assign('BAEU_CookieBarIsCurrently', Configuration::get('BAEU_CookieBarIsCurrently'));
        $this->smarty->assign('BAEU_CookieBarShowIn', Configuration::get('BAEU_CookieBarShowIn'));
        $this->smarty->assign('BAEU_OnLoad', Configuration::get('BAEU_OnLoad'));
        $this->smarty->assign('BAEU_OnHide', Configuration::get('BAEU_OnHide'));
        $this->smarty->assign('BAEU_AutoHideCookieAfterDelay', Configuration::get('BAEU_AutoHideCookieAfterDelay'));
        $this->smarty->assign('BAEU_MillisecondUntilHide', Configuration::get('BAEU_MillisecondUntilHide'));
        $this->smarty->assign('BAEU_AutoHideCookieIfScroll', Configuration::get('BAEU_AutoHideCookieIfScroll'));
        $this->smarty->assign('BAEU_ReloadAfterAcceptEvent', Configuration::get('BAEU_ReloadAfterAcceptEvent'));
        $this->smarty->assign('BAEU_CookieBarColor', Configuration::get('BAEU_CookieBarColor'));
        $this->smarty->assign('BAEU_TextColor', Configuration::get('BAEU_TextColor'));
        $this->smarty->assign('BAEU_ShowBorder', Configuration::get('BAEU_ShowBorder'));
        $this->smarty->assign('BAEU_BoderColor', Configuration::get('BAEU_BoderColor'));
        $this->smarty->assign('BAEU_leftAction', Configuration::get('BAEU_leftAction'));
        $this->smarty->assign('BAEU_leftLinkURL', Configuration::get('BAEU_leftLinkURL'));
        $this->smarty->assign('BAEU_leftOpenLinkInNewWindow', Configuration::get('BAEU_leftOpenLinkInNewWindow'));
        $this->smarty->assign('BAEU_leftLinkColor', Configuration::get('BAEU_leftLinkColor'));
        $this->smarty->assign('BAEU_leftShowAsButton', Configuration::get('BAEU_leftShowAsButton'));
        $this->smarty->assign('BAEU_leftButtonColor', Configuration::get('BAEU_leftButtonColor'));
        $this->smarty->assign('BAEU_leftHoverButtonColor', Configuration::get('BAEU_leftHoverButtonColor'));
        $this->smarty->assign('BAEU_rightLinkURL', Configuration::get('BAEU_rightLinkURL'));
        $this->smarty->assign('BAEU_rightLinkColor', Configuration::get('BAEU_rightLinkColor'));
        $this->smarty->assign('BAEU_RightOpenLinkInNewWinDown', Configuration::get('BAEU_RightOpenLinkInNewWinDown'));
        $this->smarty->assign('BAEU_rightShowAsButton', Configuration::get('BAEU_rightShowAsButton'));
        $this->smarty->assign('BAEU_rightButtonColor', Configuration::get('BAEU_rightButtonColor'));
        $this->smarty->assign('BAEU_rightHoverButtonColor', Configuration::get('BAEU_rightHoverButtonColor'));
        $this->smarty->assign('BAEU_customCss', Configuration::get('BAEU_customCss'));
        $this->smarty->assign('arr_language', $this->languageArray);
        $id_default_language = (int) (Configuration::get('PS_LANG_DEFAULT'));
        $iso_default_language = Language::getIsoById($id_default_language);
        $this->smarty->assign('id_default_language', $id_default_language);
        $this->smarty->assign('iso_default_language', $iso_default_language);
        foreach ($this->languageArray as $languageArray) {
            $BAEU_Message = html_entity_decode(Configuration::get('BAEU_Message_'.$languageArray['id_lang']));
            $BAEU_leftLinkText = Configuration::get('BAEU_leftLinkText_'.$languageArray['id_lang']);
            $BAEU_rightLinkText = Configuration::get('BAEU_rightLinkText_'.$languageArray['id_lang']);
            $this->smarty->assign('BAEU_Message_'.$languageArray['id_lang'], $BAEU_Message);
            $this->smarty->assign('BAEU_leftLinkText_'.$languageArray['id_lang'], $BAEU_leftLinkText);
            $this->smarty->assign('BAEU_rightLinkText_'.$languageArray['id_lang'], $BAEU_rightLinkText);
        }
    }
    public function hookDisplayHeader()
    {
        $this->smarty->assign('id_lang', $this->context->language->id);
        if (Configuration::get('BAEU_CookieBarIsCurrently')=="Y") {
            $this->getData();
            $this->context->controller->addCSS($this->_path.'views/css/style_frontend.css');
            if (strpos(_PS_VERSION_, "1.5") === 0) {
                $this->context->controller->addCSS($this->_path.'views/css/style_frontend_v1.5.css');
            }
            $this->context->controller->addCSS($this->_path.'views/css/font-awesome/css/font-awesome.min.css');
            $this->context->controller->addJS($this->_path.'js/cookie_law.js');
            return $this->display(__FILE__, 'views/templates/front/ba_cookie_law_info.tpl');
        }
    }
    public function hookHeader()
    {
        return $this->hookDisplayHeader();
    }
}
