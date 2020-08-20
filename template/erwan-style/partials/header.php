            <header class="header" id="content" role="main">
                <div class="navigation-trigger hidden-xl-up" data-ma-action="aside-open" data-ma-target=".sidebar">
                    <div class="navigation-trigger__inner">
                        <i class="navigation-trigger__line"></i>
                        <i class="navigation-trigger__line"></i>
                        <i class="navigation-trigger__line"></i>
                    </div>
                </div>

                <div class="header__logo hidden-sm-down">
                     <a href="index.php"><img width="220px" src="<?php echo $sysconf['template']['dir'].'/'.$sysconf['template']['theme']; ?>/img/library.png" alt=""></a>
                </div>


                <form id="simply-search" class="search" action="index.php" method="get" autocomplete="off">
                    <div class="search__inner">
                        <input type="text" id="keyword" name="keywords" value="" lang="<?php echo $sysconf['default_lang']; ?>" aria-hidden="true" autocomplete="off" class="search__text" placeholder="<?php echo __('start it by typing one or more keywords for title, author or subject'); ?>">
                        <button type="submit" name="search" value="search" class="btn zmdi zmdi-search search__helper"></button>
                    </div>
                </form>

                <ul class="top-nav">
                    <li class="hidden-xl-up"><a href="" data-ma-action="search-open"><i class="zmdi zmdi-search"></i></a></li>

                    <li><button class="btn btn-light" data-toggle="modal" data-target="#modal-large">Advance Search</button></li>

                    <li class="dropdown hidden-xs-down">
                        <a href="" data-toggle="dropdown"><i class="zmdi zmdi-translate zmdi-hc-fw"></i></a>

                        <div class="dropdown-menu dropdown-menu-right dropdown-menu--block" role="menu">
                            <div class="row app-shortcuts">
                                <a class="col-4 app-shortcuts__item" href="index.php?select_lang=en_US">
                                    <i class="zmdi zmdi-n-1-square zmdi-hc-fw"></i>
                                    <small class="">English</small>
                                    <span class="app-shortcuts__helper bg-red"></span>
                                </a>
                                <a class="col-4 app-shortcuts__item" href="index.php?select_lang=id_ID">
                                    <i class="zmdi zmdi-n-2-square zmdi-hc-fwt"></i>
                                    <small class="">Indonesia</small>
                                    <span class="app-shortcuts__helper bg-blue"></span>
                                </a>
                                <a class="col-4 app-shortcuts__item" href="index.php?select_lang=ar_AR">
                                    <i class="zmdi zmdi-n-3-square zmdi-hc-fw"></i>
                                    <small class="">Arabic</small>
                                    <span class="app-shortcuts__helper bg-teal"></span>
                                </a>
                                <a class="col-4 app-shortcuts__item" href="index.php?select_lang=de_DE">
                                    <i class="zmdi zmdi-n-4-square zmdi-hc-fw"></i>
                                    <small class="">German</small>
                                    <span class="app-shortcuts__helper bg-blue-grey"></span>
                                </a>
                                <a class="col-4 app-shortcuts__item" href="index.php?select_lang=th_TH">
                                    <i class="zmdi zmdi-n-5-square zmdi-hc-fw"></i>
                                    <small class="">Thai</small>
                                    <span class="app-shortcuts__helper bg-orange"></span>
                                </a>
                                <a class="col-4 app-shortcuts__item" href="index.php?select_lang=bn_BD">
                                    <i class="zmdi zmdi-n-6-square zmdi-hc-fw"></i>
                                    <small class="">Bengali</small>
                                    <span class="app-shortcuts__helper bg-light-green"></span>
                                </a>
                            </div>
                        </div>
                    </li>

                    <li class="dropdown hidden-xs-down">
                        <a href="" data-toggle="dropdown"><i class="zmdi zmdi-more-vert"></i></a>

                        <div class="dropdown-menu dropdown-menu-right">
                            <div class="dropdown-item theme-switch">
                                Theme Switch

                                <div class="btn-group btn-group-toggle btn-group--colors" data-toggle="buttons">
                                    <label class="btn bg-green"><input type="radio" value="green" autocomplete="off"></label>
                                    <label class="btn bg-blue  active"><input type="radio" value="blue" autocomplete="off" checked></label>
                                    <label class="btn bg-red"><input type="radio" value="red" autocomplete="off"></label>
                                    <label class="btn bg-orange"><input type="radio" value="orange" autocomplete="off"></label>
                                    <label class="btn bg-teal"><input type="radio" value="teal" autocomplete="off"></label>

                                    <div class="clearfix mt-2"></div>

                                    <label class="btn bg-cyan"><input type="radio" value="cyan" autocomplete="off"></label>
                                    <label class="btn bg-blue-grey"><input type="radio" value="blue-grey" autocomplete="off"></label>
                                    <label class="btn bg-purple"><input type="radio" value="purple" autocomplete="off"></label>
                                    <label class="btn bg-indigo"><input type="radio" value="indigo" autocomplete="off"></label>
                                    <label class="btn bg-brown"><input type="radio" value="brown" autocomplete="off"></label>
                                </div>
                            </div>
                            <a href="index.php?p=login" class="dropdown-item"><?php echo __('Librarian LOGIN'); ?></a>
                            <a href="index.php?p=visitor" class="dropdown-item"><?php echo __('Visitor Counter'); ?></a>
                            <a class="dropdown-item" Target="blank" href="https://drive.google.com/drive/folders/0By2bteaK0FsxTHI2dTBLVmNscGc"><?php echo __('PANDUAN Upload Mandiri'); ?></a>

                        </div>
                    </li>
                </ul>
            </header>
                            <!-- Advance Search -->
                            <div class="modal fade" id="modal-large" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title pull-left"><?php echo __('Advance Search'); ?></h5>
                                        </div>
                                        <div class="modal-body">
                                        <form action="index.php" method="get">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <input type="text" name="title" class="form-control form-control-lg" placeholder="<?php echo __('Title'); ?>">
                                                        <i class="form-group__bar"></i>
                                                    </div>

                                                    <div class="form-group">
                                                        <input type="email" class="form-control" placeholder="<?php echo __('Author(s)'); ?>">
                                                        <i class="form-group__bar"></i>
                                                    </div>

                                                    <div class="form-group">
                                                        <input type="text" name="subject" name="author" class="form-control form-control-sm" placeholder="<?php echo __('Subject(s)'); ?>">
                                                        <i class="form-group__bar"></i>
                                                    </div>
                                                    <div class="form-group">
                                                        <input type="text" name="isbn"class="form-control form-control-sm" placeholder="<?php echo __('ISBN/ISSN'); ?>">
                                                        <i class="form-group__bar"></i>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">

                                                    <div class="form-group">
                                                        <input type="text" name="publishyear" class="form-control form-control-sm" placeholder="<?php echo __('Year'); ?>">
                                                        <i class="form-group__bar"></i>
                                                    </div>


                                                    <div class="form-group">
                                                        <div class="select">
                                                            <select name="colltype" class="form-control form-control-lg"><?php echo $colltype_list; ?></select>
                                                            <i class="form-group__bar"></i>
                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <div class="select">
                                                            <select name="gmd" class="form-control form-control-lg"><?php echo $gmd_list; ?></select>
                                                            <i class="form-group__bar"></i>
                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <div class="select">
                                                            <select name="location" class="form-control form-control-lg"> <?php echo $location_list; ?></select>
                                                            <i class="form-group__bar"></i>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" name="search" value="search" class="btn btn-secondary btn--icon-text"><?php echo __('Search'); ?></button>
                                            <button type="button" class="btn btn-link" data-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>