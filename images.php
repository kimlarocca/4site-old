<?php require_once('Connections/cms.php'); ?>
<?php
mysqli_select_db($cms, $database_cms);
$query_photos = "SELECT * FROM photos WHERE albumID = " . $_GET['albumID'] . " ORDER BY photoSequence ASC";
$photos = mysqli_query($cms, $query_photos) or die(mysqli_error($cms));
$row_photos = mysqli_fetch_assoc($photos);
$totalRows_photos = mysqli_num_rows($photos);
?>
<?php do { ?>

    <div class="pin"><a
                href="photos-modify.php?albumID=<?php echo $row_album['albumID']; ?>&photoID=<?php echo $row_photos['id']; ?>"><img
                    src="uploads/thumb-<?php echo $row_photos['file_name']; ?>"/></a>
        <h2><?php echo $row_photos['photoTitle']; ?></h2>
        <p><?php echo $row_photos['photoDescription']; ?></p>
        <div style="margin:auto; width:100px; padding:10px 0 10px 0"><a
                    href="albums-photos.php?action=cover&albumID=<?php echo $row_album['albumID']; ?>&photoID=<?php echo $row_photos['id']; ?>"
                    class="tooltip" title="set album cover">
                <?php if ($row_photos['id'] === $coverPhotoID) { ?>
                    <img style="width:22px" src="images/heart-pink.png" width="32" height="32"/>
                <?php } else { ?>
                    <img style="width:22px" src="images/heart.png" width="32" height="32"/>
                <?php } ?>
            </a>
            <a href="photos-modify.php?albumID=<?php echo $row_album['albumID']; ?>&photoID=<?php echo $row_photos['id']; ?>"
               class="tooltip" title="update photo"><img style="width:22px" src="images/edit.png"
                                                         width="32" height="32"/></a> <a
                    href="photos-delete.php?albumID=<?php echo $row_album['albumID']; ?>&photoID=<?php echo $row_photos['id']; ?>"
                    class="tooltip" title="delete photo"><img style="width:22px"
                                                              src="images/delete.png" width="32"
                                                              height="32"/></a></div>
    </div>

<?php } while ($row_photos = mysqli_fetch_assoc($photos)); ?>
