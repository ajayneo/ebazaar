<?php class Wyomind_Pointofsale_Adminhtml_ManageController extends Mage_Adminhtml_Controller_Action { protected function _initAction() { $x50="\163\164\x72t\x6fl\x6fw\145r";
 $x51="a\162\x72\x61y\x5f\160\157\160";  $x52="\x65\x78\x70l\157\144e";  $x53="\x69\x6e\137\x61r\x72\141\x79";  $x54="im\x70lo\144e";  $x55="s\x74rl\x65\x6e";  $x56="\143\157\165nt";  $this->_title($this->__('Manage'))->_title($this->__('POS / Warehouses'));  $this->loadLayout() ->_setActiveMenu("\163\141\154\x65\x73\57\160\x6fi\156\164\157\x66\163\x61\154e");  return $this;  } public function indexAction() { $x50="s\164\x72\x74\x6f\154\157w\145\162";  $x51="a\x72\162a\x79_po\x70";  $x52="\145\170\x70\154\157\x64\145";  $x53="\x69n\x5fa\162r\141\171";  $x54="imp\x6co\144\145";  $x55="\163t\x72\154en";  $x56="\143\157\x75\x6e\x74";  $this->_initAction() ->renderLayout();  } public function importCsvAction() { $x50="\x73tr\164\x6f\x6c\157\x77er";  $x51="\x61\162\162a\x79\137\x70op";  $x52="e\170\160l\x6f\144e";  $x53="i\x6e_a\x72\x72\141\x79";  $x54="im\x70\x6c\x6f\x64e";  $x55="\x73\164\x72len";  $x56="\x63oun\x74";  $this->loadLayout();  $this->_setActiveMenu("\163\141\154\x65\x73\57\160\x6fi\156\164\157\x66\163\x61\154e");  $this->_addBreadcrumb(Mage::helper("p\157\x69\156\164of\x73\141\x6c\145")->__("\120\117\123\40\57 W\141\162\x65\150o\x75se\x73"), ("\120\117\123\40\57 W\141\162\x65\150o\x75se\x73"));  $this->getLayout()->getBlock("\x68e\141\144")->setCanLoadExtJs(true);  $this->_addContent($this->getLayout()->createBlock("\160\x6f\x69\x6e\x74\157f\163\x61le\x2f\x61\x64\155\151\x6eh\164m\x6c\x5fman\x61\x67e_\151\155\160o\162\164")) ->_addLeft($this->getLayout()->createBlock("\x70o\151n\x74\x6f\146\163\141le\x2fad\155i\x6e\150\164m\x6c\137ma\156\141\x67e\x5fi\155\160or\x74\x5f\x74\141\142\163"));  $this->renderLayout();  } public function editAction() { $x50="\x73t\x72t\157\x6co\167e\x72";  $x51="\141\162\x72\x61\x79\x5f\160\157\160";  $x52="\145\170\x70l\157d\145";  $x53="\151\156\x5f\141r\162\141\x79";  $x54="\x69\x6d\x70l\157de";  $x55="s\164\x72\x6ce\156";  $x56="c\x6fu\x6e\164";  $x38 = $this->getRequest()->getParam("\x69\x64");  $x39 = Mage::getModel("\160\x6fin\x74o\x66sa\x6c\145/\x70\x6f\x69\156\x74\157\x66\163\x61\x6c\145")->load($x38);  if ($x39->getId() || $x38 == 0) { $x3a = Mage::getSingleton("\x61\x64\155i\156\x68\x74m\154\57s\145ss\151\157\156")->getFormData(true);  if (!empty($x3a)) { $x39->setData($x3a);  } Mage::register("po\151\x6e\x74o\146\x73\141l\145\137d\x61t\x61", $x39);  $this->loadLayout();  $this->_title($this->__('Manage'))->_title($this->__('POS / Warehouses'));  $this->getLayout()->getBlock("\x68e\141\144")->setCanLoadExtJs(true);  $this->_addContent($this->getLayout()->createBlock("p\x6f\x69\x6etofs\141\154\x65\x2f\141d\155\151\156h\164\x6dl_\155a\x6e\x61ge_\x65\144\151\164")) ->_addLeft($this->getLayout()->createBlock("p\157\151\156\164\x6ffs\141\x6c\x65\x2f\141\144m\151nh\164m\154\x5f\x6d\141na\x67\x65\x5f\145\144\x69\x74\x5ft\141\x62s"));  $this->renderLayout();  } else { Mage::getSingleton("\x61\x64\155i\156\x68\x74m\154\57s\145ss\151\157\156")->addError(Mage::helper("p\157\x69\156\164of\x73\141\x6c\145")->__("\x49\x74\145m \144\x6fe\x73\x20n\x6f\x74\40\145\x78i\x73t"));  $this->_redirect("\52\57\52\57");  } } public function newAction() { $x50="st\x72t\x6f\154\x6f\x77\x65r";  $x51="\141\162ra\171\x5f\160op";  $x52="\145x\160\154\x6f\x64e";  $x53="i\156_a\x72\162\141\x79";  $x54="\x69\155p\154od\145";  $x55="\x73\x74\162le\156";  $x56="\143o\165n\164";  $this->_forward("e\144\x69t");  } public function saveAction() { $x50="s\164r\x74\157\154\157\x77\145r";  $x51="arra\171\137\160\x6f\x70";  $x52="\x65\170\160\154\157\x64e";  $x53="in\137a\162\x72\141\x79";  $x54="\151mplo\x64e";  $x55="\163t\162\154\145n";  $x56="c\157\x75\x6e\x74";  $x3b = array("\141c" => "a\143\x74i\x76ati\x6fn_\x63\157d\145", "\x61\153" => "\141c\164\x69\x76atio\x6e_\153e\171", "\142\165" => "b\x61\163\145\x5f\x75\162l", "\155d" => "\155d\65", "\x74\150" => "\x74\150\x69\163", "d\155" => "\137\144\145\x6do", "\x65x\164" => "p\x6f\x73", "\166\145r" => "\65\x2e\61.0");  $x3c = array( "\141c\164\x69\x76atio\x6e_\153e\171" => Mage::getStoreConfig("poi\x6e\164\x6ff\x73a\x6c\x65\57lic\145ns\x65/\141\x63\164\151v\x61ti\157\x6e\x5f\x6be\x79"), "a\143\x74i\x76ati\x6fn_\x63\157d\145" => Mage::getStoreConfig("\160o\x69n\164\157\146s\x61\x6c\x65\x2f\154\151\x63en\x73e\x2f\141\x63\164i\x76\141\x74\x69\x6f\x6e\137c\x6f\144\145"), "b\x61\163\145\x5f\x75\162l" => Mage::getStoreConfig("w\x65\142\57\x73e\143\165\x72e/\x62\x61\163\x65\x5fu\162l"), );  if ($x3c[$x3b['ac']] != $x3b["\155d"]($x3b["\155d"]($x3c[$x3b['ak']]) . $x3b["\155d"]($x3c[$x3b['bu']]) . $x3b["\155d"]($x3b["\x65x\164"]) . $x3b["\155d"]($x3b["\166\145r"]))) { $$x3b["\x65x\164"] = "v\x61li\x64";  $$x3b["\x74\150"]->$x3b["d\155"] = true;  } else { $$x3b["\x74\150"]->$x3b["d\155"] = false;  $$x3b["\x65x\164"] = "v\x61li\x64";  } if (!isset($$x3b["\x65x\164"]) || $$x3b["\x74\150"]->$x3b["d\155"]) $$x3b["\x74\150"]->$x3b["d\155"] = true;  if ($$x3b["\x74\150"]->$x3b["d\155"]) { $this->_getSession()->addError(Mage::helper("p\157\x69\156\164of\x73\141\x6c\145")->__("\x49\156\x76\141\154i\x64\40\154\151c\145\156\x73\145."));  Mage::getConfig()->saveConfig("\160o\x69n\164\157\146s\x61\x6c\x65\x2f\154\151\x63en\x73e\x2f\141\x63\164i\x76\141\x74\x69\x6f\x6e\137c\x6f\144\145", "", "d\x65\146\141u\154\x74", "\60");  Mage::getConfig()->cleanCache();  $this->_redirect("\52\57\52\57");  } if ($$x3b["\x74\150"]->$x3b["d\155"]) return $$x3b["\x74\150"];  if ($this->getRequest()->getPost()) { $x3a = $this->getRequest()->getPost();  if (isset($_FILES["f\x69l\145"]["\156\x61m\x65"]) && $_FILES["f\x69l\145"]["\156\x61m\x65"] != "") { $x3d = 1;  if ($x50($x51($x52("\56", $_FILES["f\x69l\145"]["\156\x61m\x65"]))) != "c\x73\x76") Mage::getSingleton("\x61\x64\155i\156\x68\x74m\154\57s\145ss\151\157\156")->addError(Mage::helper("p\157\x69\156\164of\x73\141\x6c\145")->__("\x57\162\157\156\147 fi\x6ce\40\x74\x79\x70e\x20\x28" . $_FILES["f\x69l\145"]["t\171\160e"] . ")\x2e\74\x62\162>\x43h\x6fo\163e \x61\x20\143\163\x76 f\x69l\x65\x2e"));  else { $x3e = new Varien_File_Csv;  $x3e->setDelimiter("\t");  $x3f = $x3e->getData($_FILES["f\x69l\145"]["t\155\160\137\156ame"]);  $x39 = Mage::getModel("\160\x6fin\x74o\x66sa\x6c\145/\x70\x6f\x69\156\x74\157\x66\163\x61\x6c\145");  $x40 = $x3f[0];  while (isset($x3f[$x3d])) { foreach ($x3f[$x3d] as $x41 => $x42) { $x3a[$x40[$x41]] = $x42;  } $x39->setData($x3a)->save();  $x3d++;  } } Mage::getSingleton("\x61\x64\155i\156\x68\x74m\154\57s\145ss\151\157\156")->addSuccess(Mage::helper("p\157\x69\156\164of\x73\141\x6c\145")->__(($x3d - 1) . "\40\x70\154\x61\143e\163\40\150\141\166\x65 \142\x65e\156\x20imp\x6fr\x74e\144."));  $this->_redirect("\x2a\x2f*/im\160\x6f\162\x74\103\x73v");  return;  } if (isset($x3a["\x69m\x61g\x65"]["\144el\145\164\145"]) && $x3a["\x69m\x61g\x65"]["\144el\145\164\145"] == 1) { $x3a["\x69m\x61g\x65"] = "";  } else { if (isset($_FILES["\x69m\x61g\x65"]["\156\x61m\x65"]) && $_FILES["\x69m\x61g\x65"]["\156\x61m\x65"] != "") { try { $x43 = new Varien_File_Uploader("\x69m\x61g\x65");  $x43->setAllowedExtensions(array("\x6a\x70\x67", "\x6a\160\x65\x67", "\x67\151f", "\x70ng"));  $x43->setAllowRenameFiles(true);  $x43->setFilesDispersion(false);  $x44 = Mage::getBaseDir("medi\141") . DS;  $x43->save($x44 . "\x73\x74o\162\x65\x73", $_FILES["\x69m\x61g\x65"]["\156\x61m\x65"]);  } catch (Exception $x45) { } $x3a["\x69m\x61g\x65"] = "s\x74\157\x72e\163/" . $_FILES["\x69m\x61g\x65"]["\156\x61m\x65"];  } else unset($x3a["\x69m\x61g\x65"]);  } $x39 = Mage::getModel("\160\x6fin\x74o\x66sa\x6c\145/\x70\x6f\x69\156\x74\157\x66\163\x61\x6c\145");
 if ($x53('-1', $x3a["cu\x73\x74o\155e\162\x5f\147\162\x6f\x75\160"])) $x3a["cu\x73\x74o\155e\162\x5f\147\162\x6f\x75\160"] = array("\x2d\61");  $x3a["cu\x73\x74o\155e\162\x5f\147\162\x6f\x75\160"] = $x54(',', $x3a["cu\x73\x74o\155e\162\x5f\147\162\x6f\x75\160"]);  if ($x53('0', $x3a["\163\x74\157\162\x65\137\x69\x64"])) $x3a["\163\x74\157\162\x65\137\x69\x64"] = array("\60");  $x3a["\163\x74\157\162\x65\137\x69\x64"] = $x54(',', $x3a["\163\x74\157\162\x65\137\x69\x64"]);  $x39->setData($x3a) ->setId($this->getRequest()->getParam("p\x6c\141\143\x65\137id"));  $x39->save();  try { Mage::getSingleton("\x61\x64\155i\156\x68\x74m\154\57s\145ss\151\157\156")->addSuccess(Mage::helper("p\157\x69\156\164of\x73\141\x6c\145")->__("\111\164em \167\141s\x20\163\x75\143\143\x65ss\146u\x6c\154\x79\40s\141\x76\145d"));  Mage::getSingleton("\x61\x64\155i\156\x68\x74m\154\57s\145ss\151\157\156")->setFormData(false);  if ($this->getRequest()->getParam("\x62ac\153")) { $this->_redirect("*\x2f\x2a/\145\x64\151\164", array("p\x6c\141\143\x65\137id" => $x39->getId()));  return;  } $this->_redirect("\52\57\52\57");  return;  } catch (Exception $x45) { Mage::getSingleton("\x61\x64\155i\156\x68\x74m\154\57s\145ss\151\157\156")->addError($x45->getMessage());  Mage::getSingleton("\x61\x64\155i\156\x68\x74m\154\57s\145ss\151\157\156")->setFormData($x3a);  $this->_redirect("*\x2f\x2a/\145\x64\151\164", array("p\x6c\141\143\x65\137id" => $this->getRequest()->getParam("p\x6c\141\143\x65\137id")));  return;  } } Mage::getSingleton("\x61\x64\155i\156\x68\x74m\154\57s\145ss\151\157\156")->addError(Mage::helper("p\157\x69\156\164of\x73\141\x6c\145")->__("\x55\156\x61\x62\x6ce\x20to \146\x69nd\40i\x74e\x6d\40\x74\157\40sa\x76\145"));  $this->_redirect("\52\57\52\57");  } public function deleteAction() { $x50="\163\164\162t\157\154ow\x65\x72";  $x51="a\162\162\141\x79\x5f\160\x6f\x70";  $x52="\x65\170\x70\154od\x65";  $x53="i\x6e\137a\x72\x72\141y";  $x54="im\x70\154\157\x64e";  $x55="\163t\162l\x65\156";  $x56="\x63\x6fu\156t";  if ($this->getRequest()->getParam("p\x6c\141\143\x65\137id") > 0) { try { $x39 = Mage::getModel("\160\x6fin\x74o\x66sa\x6c\145/\x70\x6f\x69\156\x74\157\x66\163\x61\x6c\145");  $x39->setId($this->getRequest()->getParam("p\x6c\141\143\x65\137id")) ->delete();  Mage::getSingleton("\x61\x64\155i\156\x68\x74m\154\57s\145ss\151\157\156")->addSuccess(Mage::helper("\x61d\155i\x6e\150\164ml")->__("Th\x65\40\x50\117\x53\x2fwa\x72\145\x68\x6f\165se\40\167\x61s \x73\165\x63c\145s\163\x66u\154ly \x64\145\x6c\x65\x74e\144"));  $this->_redirect("\52\57\52\57");  } catch (Exception $x45) { Mage::getSingleton("\x61\x64\155i\156\x68\x74m\154\57s\145ss\151\157\156")->addError($x45->getMessage());  $this->_redirect("*\x2f\x2a/\145\x64\151\164", array("p\x6c\141\143\x65\137id" => $this->getRequest()->getParam("p\x6c\141\143\x65\137id")));  } } $this->_redirect("\52\57\52\57");  } public function exportCsvAction() { $x50="\x73t\162t\x6f\154\x6f\167\145\162";  $x51="\141\x72\x72\141y\x5f\x70\157\160";  $x52="e\x78\160\154\x6f\144\x65";  $x53="\151n_a\162\x72\141\x79";  $x54="\x69mp\154\x6f\x64e";  $x55="s\164\x72\x6c\x65\156";  $x56="\143o\x75\x6et";  $x46 = "\x70\x6fin\x74\157\x66\163\x61\x6c\x65.\143\163v";  $x47 = null;  $x48 = Mage::getModel("\160\x6fin\x74o\x66sa\x6c\145/\x70\x6f\x69\156\x74\157\x66\163\x61\x6c\145")->getCollection();  $x47.="cu\x73\x74o\155e\162\x5f\147\162\x6f\x75\160" . "\t";  $x47.="\163\x74\157\162\x65\137\x69\x64" . "\t";  $x47.="o\x72\144\145\162" . "\t";  $x47.="\x73\164\x6fre_\x63\x6f\144\145" . "\t";  $x47.="\156\x61m\x65" . "\t";  $x47.="a\x64\x64\162\145ss\x5f\154\151\156e\x5f\61" . "\t";  $x47.="\141\x64dr\x65\x73\x73\x5fl\x69\156e\137\62" . "\t";  $x47.="c\x69\164\171" . "\t";  $x47.="\x73\x74a\x74e" . "\t";  $x47.="\x70\x6f\163\164\141\x6c_\143\x6f\144\145" . "\t";  $x47.="\x63\x6fu\x6e\x74\x72y\x5f\143od\145" . "\t";  $x47.="\x6d\141\x69\x6e\137p\150\157\x6ee" . "\t";  $x47.="e\155\x61\x69\154" . "\t";  $x47.="\150\157u\x72\163" . "\t";  $x47.="\x64\x65\163\143\162\151pt\151o\156" . "\t";  $x47.="\154\x6f\156gi\164\165\x64e" . "\t";  $x47.="\x6c\x61\164\151\x74ud\145" . "\t";  $x47.="s\x74a\x74\x75\163" . "\t";  $x47.="\x69m\x61g\x65" . "\t";  foreach ($x48 as $x49) { $x3f.= $x49->getData("cu\x73\x74o\155e\162\x5f\147\162\x6f\x75\160") . "\t";  $x3f.= $x49->getData("\163\x74\157\162\x65\137\x69\x64") . "\t";  $x3f.= $x49->getData("o\x72\144\145\162") . "\t";  $x3f.= $x49->getData("\x73\164\x6fre_\x63\x6f\144\145") . "\t";  $x3f.= $x49->getData("\156\x61m\x65") . "\t";  $x3f.= $x49->getData("a\x64\x64\162\145ss\x5f\154\151\156e\x5f\61") . "\t";  $x3f.= $x49->getData("\141\x64dr\x65\x73\x73\x5fl\x69\156e\137\62") . "\t";  $x3f.= $x49->getData("c\x69\164\171") . "\t";  $x3f.= $x49->getData("\x73\x74a\x74e") . "\t";  $x3f.= $x49->getData("\x70\x6f\163\164\141\x6c_\143\x6f\144\145") . "\t";  $x3f.= $x49->getData("\x63\x6fu\x6e\x74\x72y\x5f\143od\145") . "\t";  $x3f.= $x49->getData("\x6d\141\x69\x6e\137p\150\157\x6ee") . "\t";  $x3f.= $x49->getData("e\155\x61\x69\154") . "\t";  $x3f.= $x49->getData("\150\157u\x72\163") . "\t";  $x3f.= $x49->getData("\x64\x65\163\143\162\151pt\151o\156") . "\t";  $x3f.= $x49->getData("\154\x6f\156gi\164\165\x64e") . "\t";  $x3f.= $x49->getData("\x6c\x61\164\151\x74ud\145") . "\t";  $x3f.= $x49->getData("s\x74a\x74\x75\163") . "\t";  $x3f.= $x49->getData("\x69m\x61g\x65") . "\t";  $x3f.= "\x0d\x0a";  } $this->_sendUploadResponse($x46, $x47 . "\x0d\x0a" . $x3f);  } protected function _sendUploadResponse($x46, $x3f, $x4a = "\x61\x70p\154i\143\x61t\151\x6f\x6e/\157\x63\x74\x65t\55s\x74\x72e\x61m") { $x50="\163t\x72\x74o\154ow\x65\162";  $x51="arr\x61\171\137\160op";  $x52="\145x\x70\154\x6f\144e";  $x53="\151\x6e_a\x72\162\141\x79";  $x54="\151\x6d\160\154od\x65";  $x55="strl\145\x6e";  $x56="\x63\x6fu\156\x74";  $x4b = $this->getResponse();  $x4b->setHeader("\x48\124\x54\120\57\61.\61 \6200 \x4f\113", "");  $x4b->setHeader("\x50\x72\141g\155\141", "\160ub\154\x69c", true);  $x4b->setHeader("C\x61che-C\x6f\156tro\154", "\155u\x73\x74-\162\145\x76\x61\x6c\151\144\141\164\145\x2c \x70ost-ch\145c\x6b\x3d\60\x2c\40\x70re\55\143\x68\x65c\153\x3d\60", true);  $x4b->setHeader("Con\164\x65n\164-D\151\163\160\x6f\x73\151\164\x69\x6f\156", "a\164\x74\141chm\x65n\x74\73\x20\x66\x69\x6c\145\x6ea\x6d\x65=" . $x46);  $x4b->setHeader("La\163\x74-M\157\x64\x69\x66i\145\x64", date("\162"));  $x4b->setHeader("\101\x63\x63\145p\x74\x2d\x52\141\x6e\x67e\163", "\x62\x79tes");  $x4b->setHeader("C\157\x6et\145\156t\55\114e\x6eg\x74h", $x55($x3f));  $x4b->setHeader("\x43\x6f\156\x74\145\156\x74-t\171p\145", $x4a);  $x4b->setBody($x3f);  $x4b->sendResponse();
 die;  } public function stateAction() { $x50="\x73\x74\x72\164\x6f\154\157w\x65\162";  $x51="\141\162ra\171\137\160\157\x70";  $x52="\145\x78\x70\x6co\144\x65";  $x53="\x69n\x5far\x72\x61y";  $x54="\151\x6d\x70\x6c\x6f\x64e";  $x55="\163t\162\x6c\x65n";  $x56="\143\157\x75nt";  $x4c = $this->getRequest()->getParam('country');  $x4d[] = "\74o\x70\164ion v\x61\154ue\x3d\47'>P\154\x65\x61s\x65\40\x53\145\154ec\164\x3c\57\157\160t\x69\157n\x3e";  if ($x4c != '') { $x4e = Mage::getModel('directory/region')->getResourceCollection()->addCountryFilter($x4c)->load();  foreach ($x4e as $x4f) { $x4d[] = "\x3c\x6f\160ti\157\156 \166\x61\x6cu\x65\x3d\x27" . $x4f->getCode() . "'\76" . $x4f->getDefaultName() . "\74\57\x6f\160t\151o\x6e\x3e";  } } if ($x56($x4d) == 1) die("<\157\160\164\151o\156\x20\166\141l\x75\x65\75\x27\47\x3e\55\55\x2d\55--<\x2f\x6fption>");  else die($x54(' ', $x4d));  } } ; 