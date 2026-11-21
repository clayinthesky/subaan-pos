<?php foreach(['success','error'] as $kind): if($message=session()->getFlashdata($kind)): ?><div role="alert" class="notice <?= $kind ?>"><?= esc($message) ?></div><?php endif; endforeach ?>
<?php if($errors=session()->getFlashdata('errors')): ?><div role="alert" class="notice error"><ul><?php foreach($errors as $error): ?><li><?= esc($error) ?></li><?php endforeach ?></ul></div><?php endif ?>
