<?php $session = session();

if ($session->getFlashdata('error')) : ?>
    <div class="pb-2 px-3">
        <div class="alert alert-danger">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <i class="material-icons">close</i>
            </button>
            <?= $session->getFlashdata('error'); ?>
        </div>
    </div>

<?php elseif ($session->getFlashdata('success')) : ?>
    <div class="pb-2 px-3">
        <div class="alert alert-success">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <i class="material-icons">close</i>
            </button>
            <?= $session->getFlashdata('success'); ?>
        </div>
    </div>
<?php endif; ?>