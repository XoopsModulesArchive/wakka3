<?php

$wakkaResource = [
    // logged in panel

'YouAre' => 'Voi ',
'YouArePanelLink' => 'Settings',
'YouArePanelName' => 'Configura',
'LogoutLink' => 'Iesire',
'LogoutAreYouSure' => 'Iesire din sistem?',
'LogoutButton' => 'Iesire din sistem',
    // registration panel

'RegistrationWelcome' => 'Registrarea utilizatorului nou:',
'RegistrationName' => 'Alegeti NumePrenume ((WackoWiki:WackoДокументация/ЧтоТакоеИмяФамилия NumePrenume))',
'RegistrationPassword' => 'Parola (nu mai putin de 5 simboluri)',
'RegistrationButton' => 'Inainte!',
'RegistrationNameOwned' => 'Numele deja este ocupat!',
    // login panel

'LoginWelcome' => 'Entrarea',
'LoginWelcome2' => ' . . . . . . . . [[/Registration Inregistrarea]]',
'LoginName' => '((WackoWiki:WackoДокументация/ЧтоТакоеИмяФамилия ИмяФамилия))',
'LoginPassword' => 'Parola',
'LoginButton' => 'Intrarea in sistem!',
    // other

'MsWordVersion' => 'Versiunea pentru export in Microsoft Word',
'TopicSearchText' => 'Cautarea numai in anteturi',
'MailHello' => 'Noroc, ',
'MailGoodbye' => 'Cu stima, ',
'A watched Page changed!' => 'Schimbari in ',
'Someone changed this page:' => ' a modificat pagina care o supravegheati Dvs.: ',
'ActionDenied' => 'Actiune interzisa',
'TOCTitle' => 'Antetul documentului',
'SearchButtonText' => 'Cauta',
'OuterLink2' => "Link extern\n(in geam nou)",
'MailLink' => "Scrie scrisoare\n(deschide programul Dvs. postal)",
'ShowTip' => 'Apasati, pentru intoarcere la vizualizare',
'ShowText' => 'Vizualizare',
'ShowDateTimeInLinks' => 'Adauga data/timpul in linkuri',
'Typografica' => 'Corectarea tipografica la vizualizare',
'Comments_0' => 'Comentarii nu sunt',
'Comments_1' => 'Un comentariu',
'Comments_n' => 'Multe comentarii (%1).',
'Comments_all' => 'Comentarii',
'ShowComments' => 'Afiseaza comentarii/forma',
'HideComments' => 'Ascunde comentarii/forma',
'AttachComment' => 'Adauga comentarii:',
'AttachCommentButton' => 'Trimite',
'DoesNotExists' => 'Asa pagina nu exista. Doriti sa<a href="%1">creati</a> ?',
'Revision' => 'Aceasta e versiune veche <a href="%1">%2</a> de la %3.',
'ReadAccessDenied' => '<em>Nu aveti drepturi de citire a paginii date.</em>',
'YouAreOwner' => 'Posesor: Dvs. ',
'Owner' => 'Posesor: ',
'Nobody' => 'Nimeni',
'TakeOwnership' => 'A deveni posesor',
'DeleteConfirm' => "Doriti sa STERGETI textul? \\nNu va putea fi restabilit ulterior\я.",
'DeleteTip' => 'Apasati, pentru stergere.',
'DeleteText' => 'Sterge',
'EditACLConfirm' => 'A trece fara pastrarea schimbarilor?',
'EditACLText' => 'Acces...',
'EditTip' => 'Apasati, pentru a modifica pagina.',
'EditText' => 'Editare',
'RevisionTip' => 'Apasati, pentru a vedea lista schimbarilor',
'RevisionXMLTip' => 'Apasati, pentru a vedea lista schimbarilor in format XML.',
'ReferrersTip' => 'Apasati, pentru a vedea lista paginilor, ce se adreseaza la pagina curenta',
'ReferrersText' => 'Referate',
'SearchText' => 'Cautare: ',
'YouAre' => 'Dvs ',
'ACLUpdated' => 'ACL reinnoit',
'ACLGaveOwnership' => ' drepturile transmise ',
'ACLRead' => '<strong>Drepturi la citire:</strong>',
'ACLWrite' => '<strong>Drepturi la scriere:</strong>',
'ACLComment' => '<strong>Drepturi la comentarii:</strong>',
'ACLFor' => 'ACL pentru %1',
'SetOwner' => '<strong>A stabili posesorul:</strong>',
"OwnerDon'tChange" => 'Nu schima',
'ACLStoreButton' => 'A pastra',
'ACLCancelButton' => 'A renunta',
'ACLAccessDenied' => '<em>Nu sunteti posesorul paginii date.</em>',
'EditStoreButton' => 'A pastra',
'EditRe-EditButton' => 'Continua redactarea',
'EditCancelButton' => 'A renunta',
'EditPreviewButton' => 'Vizualizare preventiva',
'EditPreview' => 'Vizualizare preventiva',
'OverwriteAlert' => 'INSTIINTARE DESPRE REINSCRIERE: Pagina data a fost modificata de altcineva, in timp ce ati redactat-o.<br>Va rugam, sa copiati schimbarile Dvs. si redactati pagina din nou.',
'WriteAccessDenied' => '<em>Nu aveti dreptul de a modifica pagina data.</em>',
'EmptyComment' => 'Comentariul este nul -- nu va fi pastrat!',
'CommentAccessDenied' => '!<em>Scuzati, nu aveti drepturi la comentarea paginii date.</em>',
'YouAreNowTheOwner' => 'Acuma sunteti posesorul paginii date.',
'SimpleDiff' => 'Comparare simpla',
'ShowDifferencesButton' => 'Compara',
'CancelDifferencesButton' => 'Innapoi / Renunta',
'Comparison' => '<b>Compararea versiunilor %3 pentru %1 si %2</b>',
'SimpleDiffAdditions' => '<b>Adaugat:</b>',
'SimpleDiffDeletions' => '<b>Lichidat:</b>',
'History' => 'Istoria modifarilor',
'ReEditOldRevision' => 'Redactarea versiunii date, vechi',
'ExternalPages' => '<strong>Pagini externe, ce se adreseaza la &nbsp;%1</strong><br><small> (%2) (<a href="%3" >lista site-urilor</a>):</small>',
'ExternalPages(Global)' => '<strong>Pagini externe, ce se adreseaza la&nbsp;' . $wakkaConfig['wakka_name'] . '</strong><br><small> (<a href="%1">lista site-urilor</a>):</small>',
'Domains/SitesPages' => '<strong>Site-uri, на которых есть ссылки на&nbsp;%1</strong><br><small> (%2) (<a href="%3" >lista separata a paginilor</a>):</small>',
'Domains/SitesPages(Global)' => '<strong>Site-uri, pe care sunt link-uri la&nbsp;' . $wakkaConfig['wakka_name'] . '</strong><br><small> (<a href="%1">lista separata a paginilor</a>):</small>',
'Last24Hours' => 'pentru ultimele 24 ore',
'LastDays' => 'pentru ultimele %1 zile',
'ViewReferringSites' => '<a href="%1">Site-uri, ce se adreseaza la %2</a>',
'ViewReferrersFor' => '<a href="%1">Link-uri la %2</a>',
'ViewReferringSites(Global)' => '<a href="%1">Site-uri, ce se adreseaza la ' . $wakkaConfig['wakka_name'] . '</a>',
'ViewReferrersFor(Global)' => '<a href="%1">Link-uri la ' . $wakkaConfig['wakka_name'] . '</a>',
'NoneReferrers' => '<em>Nu</em>',
'NotOwnerAndCanDelete' => '<em>Nu sunteti posesorul paginii date si nu o puteti sterge</em>',
'ReferrersRemoved' => '<em>Link-uri externe la pagina %1 sunt lichidate.</em>',
'LinksRemoved' => '<em>Link-urile locale с %1 sunt lichidate.</em>',
'AclsRemoved' => '<em>Drepturile de acces %1 sunt lichidate.</em>',
'PageRemoved' => '<em>Pagina %1 este lichidata.</em>',
'ThisActionHavenotUndo' => '<h3>Lichidarea nu poate fi oprita.</h3>',
'PrintVersion' => 'Versiune pentru imprimare',
'ReferringPages' => 'Paginile, ce se adreseaza la pagina data',
'NoReferringPages' => '<em>Referinte la pagina data nu sunt</em>',
'NoAccessToSourcePage' => '<em>Acces la pagina, adresata din Actiuni este interzisa.</em>',
'SourcePageDoesntExist' => 'Pagina, adresata din Actiuni, inca nu exista.',
'NotLoggedInThusEdited' => '<em>Nu sunteti inregistrat(a) in sistem, paginile modificate de Dvs. nu pot fi gasite.</em>',
'DidntEditAnyPage' => '<em>Nu ati redactat nimic.</em>',
'NoPagesFound' => '<em>Cautarea nu are nici un rezultat.</em>',
'MyChangesTitle1' => 'Este lista paginilor modificate de Dvs, sortate dupa timpul modificarilor Dvs.',
'MyChangesTitle2' => 'Este lista paginilor modificate de Dvs cu indicarea timpului ultimei modificari a Dvs.',
'OrderABC' => 'sorteaza dupa alfabet',
'OrderDate' => 'sorteaza dupa data crearii',
'OrderChange' => 'sorteaza dupa data modificarii',
'ListOwnedPages' => 'Paginile, care le posedati (in ordine alfabetica)',
'ListOwnedPages2' => 'Paginile, care le posedati (conform datei crearii)',
'ListOwnedPages3' => 'Paginile, care le posedati (conform datei modificarii)',
'YouDontOwn' => '<em>Nu posedati nici o pagina.</em>',
'NotLoggedInThusOwned' => '<em>Nu sunteti inregistrat(a) in sistema, painile pe care le posedati nu pot fi gasite.</em>',
'NoOrphaned' => '<em>Nu sunt pagini pierdute. Perfect!</em>',
'PagesLinkingTo' => 'Paginile, ce se adreseaza la ',
'NoPageLinkingTo' => 'Nu sunt pagini de referinta',
'NoWantedPages' => '<em>Nu sunt pagini completate. Perfect!</em>',
'NoRecentComments' => '<em>Comentarii nu sunt de demult.</em>',
'LatestCommentBy' => 'ultimul a comentat',
'NoRecentlyCommented' => '<em>Comentarii nu sunt de demult.</em>',
'SearchResults' => 'Rezultatul cautarii frazei ',
'NotFound' => 'nu este gasit',
'NoResultsFor' => 'Nu sunt rezltate pentru fraza ',
'SearchResultsFor' => 'Rezultatul cautarii frazei ',
'SearchFor' => 'A cauta',
'TopicSearchResults' => 'Rezultatul cautarii in anteturi ',
'NotFoundInTopics' => 'nu se intalneste in anteturi',
'RemoveWatch' => 'Scoate monitorizarea',
'SetWatch' => 'Monitorizeaza',
'history' => 'Istoria',
'SettingsStored' => 'Preferintele Dvs. sunt salvate',
'LoggedOut' => 'Ati iesit din sistema',
'Hello' => 'Salut',
'YourEmail' => 'Adresa postei electronice',
'YourMotto' => 'Motto-ul Dvs.',
'RevisionListLimit' => 'Limitarea listei pagini redactate',
'RecentChangesLimit' => 'Limitarea listei paginilor modificate',
'ShowComments?' => 'Afiseaza comentariile implicit',
'DoubleclickEditing' => 'Editarea prin double-click',
'UpdateSettingsButton' => 'Pastreaza preferintele',
'WrongPassword' => 'Parola incorecta!',
'AlreadyRegistered' => 'Daca deja sunteti inregistrat, atunci intrati aici!',
'YourLogin' => 'WikkiNume al Dvs.',
'Password' => 'Parola (nu mai putin de 5 simboluri)',
'StuffYouNeed' => 'Aceste cimpuri trebuie de completat, numai daca va inregistrati pentru prima data (este creat un utilizator nou).',
'ConfirmPassword' => 'Confirarea parolei',
'Email' => 'E-mail',
'MustBeWikiName' => 'Numele de utilizator trebuie sa fie ((WackoWiki:WackoДокументация/ВикиИмя ВикиИменем)) ',
'SpecifyEmail' => 'Trebuie sa indicati adresa e-mail.',
'NotAEmail' => 'Aceasta nu este o adresa e-mail.',
'PasswordsDidntMatch' => 'Parolele nu coincid.',
'SpacesArentAllowed' => 'Nu se poate de folosit spatzii libere in parole.',
'PasswordTooShort' => 'Parola este prea skurta.',
'SeeListOfPages' => 'Lista paginilor voastre, shi lista paginilor kare lea-tzi redactat.',
'AddToBookmarks' => 'Adauga in meniul personal',
'YourBookmarks' => 'Meniul dumneavoastra personal',
'YourLanguage' => 'Alegetzi limba',
'ShowSpaces' => 'Arata spatziile libere in в ВикиИменах',
'WrongPage4Redirect' => 'Readresarea nu poate fi efectuata din kauza parametrilor incorectzi de readresare',
'ReallyDelete' => 'Intr-adevar doriti sa stergeti pagina?<br> Restabilirea posterioara v-a fi imposibila. <br><br>ATENTIE: la stergerea paginii se vor inlatura toate versiunile ei, comentariile, referurile etc.',
'RemoveButton' => 'Da, de sters!',
'Comment for watched page' => 'Comentariu la pagina ',
'Someone commented' => ' am comentat pagina cercetata de Dvs.: ',
'AlertReferringPages' => 'Referintele la pagina data sunt pe paginile',
'EditIcon' => '(editare)',
'LinksTree:Level>4 warning' => 'Дерево ссылок не может иметь больше 4 уровней',
'LinksTree:Title' => 'Lista de lincuri',
'Tree:ClusterTitle' => 'Дерево кластера %1',
'Tree:SiteTitle' => 'Structura saitului',
'Tree:Empty' => '%1&nbsp;не содержит подстраниц.',
'search_title_help' => '',
'RenameText' => 'Redenumeste...',
'NewName' => 'Introduceti nume nou pentru pagina data',
'RenameButton' => 'Redenumeste!',
'AlredyExists' => '<em>Pagina cu numele <strong>%1</strong> deja exista.</em>',
'AlredyNamed' => '<em>Pagina data deja se numeste <strong>%1</strong>.</em>',
'AclsRenamed' => '<em>Drepturile de acces %1 deja sunt modificate.</em>',
'PageRenamed' => '<em>Pagina %1 deja e redenumita.</em>',
'NewNameOfPage' => 'Numele nou al paginii: ',
'NotOwnerAndCantRename' => '<em>Nu sunteti posesorul paginii date si nu o puteti redenumi</em>',
'NeedRedirect' => 'A stabili la pagina veche redirectionare la pagina noua',
'RedirectCreated' => '<em>Pe pagina %1 este creat redirect la pagina noua</em>',
'LinksRenamed' => '<em>Link-urile de pe %1 sunt redenumite.</em>',
'ChooseTheme' => 'Alegeti tema',
'CreatePage' => 'Creaza pagina',
'RemoveFromBookmarks' => 'Удалить из личного меню',
'DontRedirect' => 'Не давать действию Redirect<br> автоматически перенаправлять',
'SendWatchMail' => 'Отсылать уведомления<br> при обновлении наблюдаемых Вами страниц',
'PageMoved' => 'Эта страница перенесена и доступна теперь по адресу',
'CurrentPassword' => 'Введите текущий пароль',
'NewPassword' => 'Новый пароль<br>(не короче 5 символов)',
'YouWantChangePassword' => 'Смена пароля',
'PasswordChanged' => 'Пароль успешно изменён',
'Mail.Welcome' => 'Регистрация в ',
'Mail.Registered' => 'Вы успешно зарегистрировались в %1.<br>Ваш логин: %2<br>Чтобы получать уведомления, Вам необходимо подтвердить ваш электронный адрес. Чтобы сделать это, Apasati <a href=%3>на ссылку</a>.',
'Mail.Verify' => 'Кто-то, возможно, Вы, указал Ваш электронный адрес в %1.<br>Ваш логин: %2<br>Чтобы получать уведомления, Вам необходимо подтвердить ваш электронный адрес. Чтобы сделать это, Apasati <a href=%3>на ссылку</a>.',
'Mail.Confirm' => 'Запрос на подтверждение email',
'EmailConfirmed' => 'Ваш email успешно подтверждён.',
'EmailNotConfirmed' => 'К сожалению, ваш email не может быть подтверждён.',
'ForgotComment' => "Introdu loginul sau e-mailul\n si vei primi scrisoarea cu instructiuni.",
'ForgotMain' => 'Ai uitat parola?',
'ForgotField' => '((WackoWiki:WackoДокументация/ЧтоТакоеИмяФамилия ИмяФамилия)) или адрес электронной почты',
'SendButton' => 'Trimite!',
'UserNotFound' => '<em>Nu exista utilizator cu asa login sau e-mail.</em>',
'NotConfirmedMail' => '<em>Ваш электронный адрес не подтверждён.<br>Невозможно выслать код по неподтверждённому адресу.</em>',
'Mail.ForgotSubject' => 'Восстановление пароля в ',
'Mail.ForgotMessage' => 'Кто-то забыл Ваш пароль в %1.<br>Ваш логин: %2<br>Чтобы изменить Ваш пароль, вам необходимо щелкнуть по указанной ссылке или скопировать ее в адресную строку браузера.<br><a href="%3">%3</a>',
'CodeWasSent' => 'Инструкция по восстановлению пароля отправлена Вам по электронной почте.',
'WrongCode' => '<em>Неверный код</em>',
'YouWantChangePasswordForUser' => 'Смена пароля pentru пользователя %1',
'Watchers' => 'Список наблюдающих за %1',
'NoWatchers' => '<em>За страницей %1 никто не наблюдает</em>',
'NotOwnerAndViewWatchers' => '<em>Вы не владеете cтраницей %1 и поэтому не можете получить список наблюдающих за ней</em>',
'SetLang' => 'Выберите язык Paginile',
'MeasuredTime' => 'Время работы',
    //Settings

'SettingsFor' => 'Bloc de reglare %1',
'metaDesc' => 'Ключевые слова/Описание &ndash; поля, вставляемые в HTML-заголовок каждой Paginile (META).',
'meta1' => 'Ключевые слова',
'meta2' => 'Descriere',
'SettingsText' => 'Proprietati',
'SettingsTip' => 'Alte proprietati si acorduri a pagenii curente',
'SettingsPortal' => 'Alte acorduri si actiuni asupra paginii:',
'SettingsEdit' => 'Redactare',
'SettingsRevisions' => 'Versiunii',
'SettingsRename' => 'Reоntitulare',
'SettingsRemove' => 'Удаление',
'SettingsAcls' => 'Acces',
'SettingsUpload' => 'Загрузка файлов',
'SettingsReferrers' => 'Lincuri',
'SettingsWatch' => 'Наблюдать',
'SettingsPrint' => 'Versiune pentru печати',
'SettingsMsword' => 'Versiune pentru MS Word',
'SettingsMassAcls' => 'ACLs for entire cluster',
    // upload

'UploadFor' => 'Incarcarea fishierului',
'UploadButtonText' => 'Загрузить',
'UploadGlobalText' => 'будет доступен всем посетителям',
'UploadHereText' => 'только читателям данной Paginile',
'UploadDesc' => 'описание',
'UploadForbidden' => 'Вам запрещено загружать сюда файлы.',
'UploadNoFile' => 'Почему-то в форме не был передан файл',
'UploadMaxSizeReached' => 'Файл слишком большого размера!',
'UploadNotAPicture' => 'Разрешено загружать только картинки, а вы что послали?',
'UploadMaxFileCount' => 'Вы превысили квоту загружаемых файлов',
'UploadDone' => 'Успешно загружен файл',
'UploadKB' => 'Kb',
'UploadFileNotFound' => 'Fisierul nu exista',
'UploadFileForbidden' => 'Acces la fisier interzis',
'UploadTitle' => 'Файлы, доступные на данной странице',
'UploadTitleGlobal' => 'Файлы, доступные с любой Paginile',
'UploadRemove' => 'sterge',
'UploadRemovedFromDB' => 'Файл удалён из базы данных',
'UploadRemovedFromFS' => 'Файл стёрт с жёсткого диска хостинга',
'UploadRemovedFromFSError' => 'Не удалось физически удалить файл!',
'UploadRemoveConfirm' => 'Sterge fisier?',
'UploadRemoveDenied' => 'Nu aveti permisiune de stergere a fisierului',
'Files_0' => 'There is no files on this page.',
'Files_1' => 'There is one file on this page.',
'Files_n' => 'There are %1 files on this page.',
'Files_all' => 'Files',
'ShowFiles' => 'Display files/form',
'HideFiles' => 'Hide files/form',
'ShowFiles?' => 'Show files lists by default',
'RegistrationLang' => 'Your language',
'BadName' => 'Chosen name is invalid',
'PleaseLogin' => '((Settings Login here,please))',
'Bookmarks' => 'Bookmarks',
'ReallyDeleteComment' => 'Do you really want to DELETE comment?<br>There is no undo for this action!',
'ForgotLink' => 'Forgot password?',
'RegistrationClosed' => 'Registration is not available here. Try to contact site administrator.',
'ACLForCluster' => 'Access Control Lists for ENTIRE cluster %1',
'ACLAreYouSure' => 'You are going to change ACLs of this page and all of its subpages. Continue?',
'MeasuredMemory' => 'Memory',
'default_bookmarks' => "PageIndex\nRecentChanges\nRecentlyCommented\n((Users))\n((Registration))",
'site_bookmarks' => 'PageIndex / RecentChanges / RecentlyCommented',
    //icons

'outericon' => '<img src="{theme}icons/web.gif" alt="" border="0">',
'fileicon' => '<img src="{theme}icons/file.gif" alt="" border="0">',
'mailicon' => '<img src="{theme}icons/mail.gif" alt="" border="0">',
'lockicon' => '<img src="{theme}icons/lock.gif" align="middle" hspace="2" alt="Nu aveti acces" border="0">',
'keyicon' => '<img src="{theme}icons/key.gif" align="middle" hspace="2" alt="Pagina cu acces limitat" border="0">',
'wantedicon' => '?',
];
