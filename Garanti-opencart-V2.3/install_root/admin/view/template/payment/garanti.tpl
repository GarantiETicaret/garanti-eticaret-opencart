<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right">
                <button type="submit" form="form-garanti-settings" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
                <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
            <h1>Kredi Kartı ile Ödeme Ayarları</h1>
            <ul class="breadcrumb">
                <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
                <?php } ?>
            </ul>
        </div>
    </div>

    <div class="container-fluid">

   
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-pencil"></i> Ödeme Entegrasyon Bilgileriniz </h3>
            </div>

            <div class="panel-body">
                <ul class="nav nav-tabs" id="tabs">
                    <li class="active"><a href="#tab-garanti_settings" data-toggle="tab">Genel Ayarlar</a></li>
                
                    <li><a href="#tab-garanti_help" data-toggle="tab">Yardım</a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="tab-garanti_settings">
                        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-garanti-settings" class="form-horizontal">

                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="garanti_userId"><span data-toggle="tooltip" title="<?php echo $help_total; ?>">Garanti User Id</span></label>
                                <div class="col-sm-10">
                                    <input type="text" name="garanti_userId" value="<?php echo $garanti_userId; ?>"  id="garanti_userId" class="form-control" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="garanti_provuserid"><span data-toggle="tooltip" title="<?php echo $help_total; ?>">Garanti ProvUserId</span></label>
                                <div class="col-sm-10">
                                    <input type="text" name="garanti_provuserid" value="<?php echo $garanti_provuserid; ?>"  id="garanti_provuserid" class="form-control" />
                                </div>
                            </div>
							<div class="form-group">
                                <label class="col-sm-2 control-label" for="garanti_merchantid"><span data-toggle="tooltip" title="<?php echo $help_total; ?>">Garanti Terminal MerchantId</span></label>
                                <div class="col-sm-10">
                                    <input type="text" name="garanti_merchantid" value="<?php echo $garanti_merchantid; ?>"  id="garanti_merchantid" class="form-control" />
                                </div>
                            </div>
							<div class="form-group">
                                <label class="col-sm-2 control-label" for="garanti_teminalid"><span data-toggle="tooltip" title="<?php echo $help_total; ?>">Garanti Terminal Id</span></label>
                                <div class="col-sm-10">
                                    <input type="text" name="garanti_teminalid" value="<?php echo $garanti_teminalid; ?>"  id="garanti_teminalid" class="form-control" />
                                </div>
                            </div>
							<div class="form-group">
                                <label class="col-sm-2 control-label" for="garanti_password"><span data-toggle="tooltip" title="<?php echo $help_total; ?>">Garanti Password</span></label>
                                <div class="col-sm-10">
                                    <input type="text" name="garanti_password" value="<?php echo $garanti_password; ?>"  id="garanti_password" class="form-control" />
                                </div>
                            </div>
							<div class="form-group">
                                <label class="col-sm-2 control-label" for="garanti_baseurl"><span data-toggle="tooltip" title="<?php echo $help_total; ?>">Garanti BaseUrl</span></label>
                                <div class="col-sm-10">
                                    <input type="text" name="garanti_baseurl" value="<?php echo $garanti_baseurl; ?>"  id="garanti_baseurl" class="form-control" />
                                </div>
                            </div>
							<div class="form-group">
                                <label class="col-sm-2 control-label" for="garanti_env_tab"><span data-toggle="tooltip" title="<?php echo $help_total; ?>">Çalışma Ortamı</span></label>
                                <div class="col-sm-10">
                                    <select name="garanti_env_tab" id="input-garanti_env_tab" class="form-control">              
                                        <option value="Test">Test</option>
                                        <option value="Prod" <?php if ($garanti_env_tab == 'Prod') { ?>selected="selected"<?php } ?>>Prod</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="garanti_ins_tab"><span data-toggle="tooltip" title="<?php echo $help_total; ?>">Taksitli İşlem</span></label>
                                <div class="col-sm-10">
                                    <select name="garanti_ins_tab" id="input-garanti_ins_tab" class="form-control">              
                                        <option value="on">Evet</option>
                                        <option value="off" <?php if ($garanti_ins_tab == 'off') { ?>selected="selected"<?php } ?>>Hayır</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="input-order-status">Ödeme Yöntemi</label>
                                <div class="col-sm-10">
                                    <select name="garanti_3d_mode" id="input-garanti_3d_mode" class="form-control">              
                                        <option value="shared3d" <?php if ($garanti_3d_mode == 'shared_3D') { ?>selected="selected"<?php } ?>>Ortak Ödeme Sayfası (3DS li) </option>
                                        <option value="shared" <?php if ($garanti_3d_mode == 'shared') { ?>selected="selected"<?php } ?>>Ortak Ödeme Sayfası (3DS siz)  </option>
                                        <option value="form" <?php if ($garanti_3d_mode == 'form') { ?>selected="selected"<?php } ?>>Form ile Ödeme (3DS siz)</option>
										<option value="form3d" <?php if ($garanti_3d_mode == 'form3d') { ?>selected="selected"<?php } ?>>Form ile Ödeme (3DS li)</option>
                                    </select>
                                </div>
                            </div>
							<div class="form-group">
                                <label class="col-sm-2 control-label" for="garanti_3dsec_tab"><span data-toggle="tooltip" title="<?php echo $help_total; ?>">3D Güvenlik Seviyesi</span></label>
                                <div class="col-sm-10">
                                    <select name="garanti_3dsec_tab" id="input-garanti_3dsec_tab" class="form-control">              
                                        <option value="3D_OOS_PAY">3D_OOS_PAY</option>
                                        <option value="3D_OOS_FULL" <?php if ($garanti_3dsec_tab == '3D_OOS_FULL') { ?>selected="selected"<?php } ?>>3D_OOS_FULL</option>
										<option value="3D_OOS_HALF" <?php if ($garanti_3dsec_tab == '3D_OOS_HALF') { ?>selected="selected"<?php } ?>>3D_OOS_HALF</option>
                                    </select>
                                </div>
                            </div>
                             <div class="form-group">
                                <label class="col-sm-2 control-label" for="input-status">Modül Durumu</label>
                                <div class="col-sm-10">
                                    <select name="garanti_status" id="input-status" class="form-control">                
                                        <option value="1" selected="selected">Aktif</option>
                                        <option value="0" <?php if (!$garanti_status) { ?> checked="checked" <?php } ?> >Pasif</option>
                                    </select>
                                </div>
                            </div>
                           <div class="form-group">
                                <label class="col-sm-2 control-label" for="input-status">Sipariş Durumu</label>
                                <div class="col-sm-10">
                                    <select name="garanti_order_status_id" id="input-order-status" class="form-control">
                                        <?php foreach ($order_statuses as $order_status) { ?>
                                        <?php if ($order_status['order_status_id'] == $garanti_order_status_id) { ?>
                                        <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                                        <?php } else { ?>
                                        <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                                        <?php } ?>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
								<input type="hidden" name="garanti_submit" value="1"/>
                    </div>
                
  
                    <div class="tab-pane" id="tab-garanti_help">
						<div class="panel">
							<div class="row garanti-header">
								<img src="../catalog/view/theme/default/image/garanti/garanti_logo.png" class="col-sm-2 text-center" id="payment-logo">
								<div class="col-sm-6 text-center text-muted">
								Teknik ve diğer sorularınız için yandaki butonlar ile iletişime geçebilirsiniz.
								</div>
								<div class="col-sm-4 text-center">
									<a class="btn btn-primary" href="https://www.garanti.com.tr/">garanti.com.tr</a>
									<a class="btn btn-primary" href="https://developer.garanti.com.tr">garanti Developer</a>
								</div>
							</div>

							
						</div>					
					
					
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php echo $footer; ?>
<style>
    #content .tab-pane:first-child .panel {
        border-top-left-radius: 0;
    }

    .garanti-header .text-branded,
    .garanti-content .text-branded {
        color: #00aff0;
    }

    .garanti-header h4,
    .garanti-content h4,
    .garanti-content h5 {
        margin: 2px 0;
        color: #00aff0;
        font-size: 1.8em;
    }

    .garanti-header h4 {
        margin-top: 5px;
    }

    .garanti-header .col-md-6 {
        margin-top: 18px;
    }

    .garanti-content h4 {
        margin-bottom: 10px;
    }

    .garanti-content h5 {
        font-size: 1.4em;
        margin-bottom: 10px;
    }

    .garanti-content h6 {
        font-size: 1.3em;
        margin: 1px 0 4px 0;
    }

    .garanti-header > .col-md-4 {
        height: 65px;
        vertical-align: middle;
        border-left: 1px solid #ddd;
    }

    .garanti-header > .col-md-4:first-child {
        border-left: none;
    }

    .garanti-header #create-account-btn {
        margin-top: 14px;
    }

    .garanti-content dd + dt {
        margin-top: 5px;
    }

    .garanti-content ul {
        padding-left: 15px;
    }

    .garanti-content .ul-spaced li {
        margin-bottom: 5px;
    }
    table.garanti_table {
        width:90%;
        margin:auto;
    }
    table.garanti_table td,th {
        width: 60px;
        margin:0px;
        padding:2px;
    }
    table.garanti_table input[type="number"] {
        width:50px;
    }
</style>