<!-- Start UberGallery <?php echo UberGallery::VERSION; ?> - Copyright (c) <?php echo date('Y'); ?> Chris Kankiewicz (http://www.ChrisKankiewicz.com) -->
<div id="galleryWrapper">

    <?php if (!empty($images) && $stats['total_images'] > 0): ?>

        <ul id="galleryList" class="clearfix">

            <?php foreach ($images as $image): ?>
                <li><a href="<?php

                if (isset($siteUrl) && !empty($siteUrl)) {
                    echo $siteUrl . '/' . $image['file_path'];
                } else {
                    echo $image['file_path'];
                }

                ?>" title="<?php echo $image['file_title']; ?>" rel="<?php echo $relText; ?>"><img src="<?php

                if (isset($siteUrl) && !empty($siteUrl)) {
                    echo $siteUrl . '/' . $image['thumb_path'];
                } else {
                    echo $image['thumb_path'];
                }

                ?>" alt="<?php echo $image['file_title']; ?>"/></a></li>
            <?php endforeach; ?>

        </ul>

    <?php else: ?>

        <p>No images found.</p>

    <?php endif; ?>


    <div id="galleryFooter" class="clearfix">

        <?php if ($stats['total_pages'] > 1): ?>

            <ul id="galleryPagination">

                <?php foreach ($paginator as $item): ?>
                    <li class="<?php echo $item['class']; ?>">
                        <?php if (!empty($item['href'])): ?>
                            <a href="<?php echo $item['href']; ?>"><?php echo $item['text']; ?></a>
                        <?php else: ?>
                            <?php echo $item['text']; ?>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>

            </ul>

        <?php endif; ?>

        <br /><div id="credit">Powered by, <a href="<?php echo SIMPLICITY_URL;?>">Simplicity</a></div>

    </div>

</div>
<!-- End UberGallery - Distributed under the MIT license: http://www.opensource.org/licenses/mit-license.php -->