<center><h5><?= _lang("settings_info"); ?></h5></center>
<br/>
<hr/><br/>

<form method="post" action="" class="form-horizontal row-border">
    <div class="form-group row">
        <h6 class="col-sm-3"><?= _lang("username"); ?>: </h6>
        <div class="col-sm-8">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-user"></i> </span>
                </div>
                <input
                    type="text"
                    name="username"
                    class="form-control"
                    placeholder="mahid_hm"
                    value="<?= $_SESSION['username'] ?? $_POST['username'] ?? '' ?>"
                    required
                >
            </div>
        </div>
    </div>
    <div class="form-group row">
        <h6 class="col-sm-3"><?= _lang("password"); ?>: </h6>
        <div class="col-sm-8">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-key"></i></span>
                </div>
                <input
                    type="password"
                    name="password"
                    class="form-control"
                    placeholder=""
                    value="<?= $_SESSION['password'] ?? $_POST['username'] ?? '' ?>"
                    required
                >
            </div>
        </div>
    </div>

    </div>
    <div class="card-footer">
        <div class="row">
            <center>
                <a href="database.php" class="btn-secondary btn">
                    <i class="fas fa-arrow-left"></i>
                    <?= _lang("back"); ?>
                </a>
                <input class="btn-primary btn" type="submit" name="submit" value="<?= _lang("next"); ?>"/>
            </center>
        </div>
    </div>
</form>