<?php

$author_field = $this->skylight_utilities->getField("Author");
$title_field = $this->skylight_utilities->getField("Title");
$maker_field = $this->skylight_utilities->getField("Maker");
$type_field = $this->skylight_utilities->getField("Type");
$bitstream_field = $this->skylight_utilities->getField("Bitstream");
$thumbnail_field = $this->skylight_utilities->getField("Thumbnail");
$filters = array_keys($this->config->item("skylight_filters"));
$link_uri_field = $this->skylight_utilities->getField("Link");
$short_field = $this->skylight_utilities->getField("Short Description");
$date_field = $this->skylight_utilities->getField("Date");
$media_uri = $this->config->item("skylight_media_url_prefix");
$theme = $this->config->item("skylight_theme");

$type = 'Unknown';
$mainImageTest = false;
$numThumbnails = 0;
$bitstreamLinks = array();
$image_id = "";

// booleans for video/audio
$mainImage = false;
$videoFile = false;
$audioFile = false;

if(isset($solr[$bitstream_field]) && $link_bitstream) {

    foreach ($solr[$bitstream_field] as $bitstream_for_array) {
        $b_segments = explode("##", $bitstream_for_array);
        $b_seq = $b_segments[4];
        $bitstream_array[$b_seq] = $bitstream_for_array;
    }

    ksort($bitstream_array);

    $mainImage = false;
    $videoFile = false;
    $audioFile = false;
    $audioLink = "";
    $videoLink = "";
    $b_seq = "";

    foreach ($bitstream_array as $bitstream) {
        $mp4ok = false;
        $b_segments = explode("##", $bitstream);
        $b_filename = $b_segments[1];
        if ($image_id == "") {
            $image_id = substr($b_filename, 0, 7);
        }
        $b_handle = $b_segments[3];
        $b_seq = $b_segments[4];
        $b_handle_id = preg_replace('/^.*\//', '', $b_handle);
        $b_uri = './record/' . $b_handle_id . '/' . $b_seq . '/' . $b_filename;

        if ((strpos($b_uri, ".mp3") > 0) or (strpos($b_uri, ".MP3") > 0)) {
            $audioLink .= '<audio controls>';
            $audioLink .= '<source src="' . $b_uri . '" type="audio/mpeg" />Audio loading...';
            $audioLink .= '</audio>';
            $audioFile = true;
        } else if ((strpos($b_filename, ".mp4") > 0) or (strpos($b_filename, ".MP4") > 0)) {
            $b_uri = $media_uri . $b_handle_id . '/' . $b_seq . '/' . $b_filename;
            // Use MP4 for all browsers other than Chrome
            if (strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome') == false) {
                $mp4ok = true;
            } //Microsoft Edge is calling itself Chrome, Mozilla and Safari, as well as Edge, so we need to deal with that.
            else if (strpos($_SERVER['HTTP_USER_AGENT'], 'Edge') == true) {
                $mp4ok = true;
            }
            if ($mp4ok == true) {
                $videoLink .= '<div class="flowplayer" data-analytics="' . $ga_code . '" title="' . $record_title . ": " . $b_filename . '">';
                $videoLink .= '<video preload=auto loop width="100%" height="auto" controls preload="true" width="660">';
                $videoLink .= '<source src="' . $b_uri . '" type="video/mp4" />Video loading...';
                $videoLink .= '</video>';
                $videoFile = true;
            }
        } else if ((strpos($b_filename, ".webm") > 0) or (strpos($b_filename, ".WEBM") > 0)) {
            //Microsoft Edge needs to be dealt with. Chrome calls itself Safari too, but that doesn't matter.
            if (strpos($_SERVER['HTTP_USER_AGENT'], 'Edge') == false) {
                if (strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome') == true) {
                    $b_uri = $media_uri . $b_handle_id . '/' . $b_seq . '/' . $b_filename;
                    // if it's chrome, use webm if it exists
                    $videoLink .= '<div class="flowplayer" data-analytics="' . $ga_code . '" title="' . $record_title . ": " . $b_filename . '">';
                    $videoLink .= '<video preload=auto loop width="100%" height="auto" controls preload="true" width="660">';
                    $videoLink .= '<source src="' . $b_uri . '" type="video/webm" />Video loading...';
                    $videoLink .= '</video>';
                    $videoFile = true;
                }
            }
        }
    }
}
?>

<nav class="navbar navbar-fixed-top second-navbar">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
        </div>
        <div>
            <div class="collapse navbar-collapse" id="myNavbar">
                <ul class="nav navbar-nav">
                    <li><a href="<?php echo $_SERVER['REQUEST_URI'];?>#stc-section1">Top</a></li>
                    <li><a href="<?php echo $_SERVER['REQUEST_URI'];?>#stc-section2">Image</a></li>
                    <li><a href="<?php echo $_SERVER['REQUEST_URI'];?>#stc-section3">Categories</a></li>
                    <?php if($audioLink != '') {
                        echo '<li ><a href ="'.$_SERVER['REQUEST_URI'].'#stc-section4" > Audio</a ></li >';
                    } ?>
                    <li><a href="<?php echo $_SERVER['REQUEST_URI'];?>#stc-section5">More Data</a></li>
                    <li><a href="<?php echo $_SERVER['REQUEST_URI'];?>#stc-section6">Related Instruments</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>
<?php


foreach($recorddisplay as $key)
{
    $element = $this->skylight_utilities->getField($key);

    if(isset($solr[$element]))
    {
        foreach($solr[$element] as $index => $metadatavalue)
        {
            // if it's a facet search
            // make it a clickable search link

            if($key == 'Date Made') {
                $date = $metadatavalue;
            }
            if (!(isset($date))){
                $date = 'Undated';
            }

            if($key == 'Maker') {
                $maker = $metadatavalue;
            }
            if (!(isset($maker))){
                $maker = 'Unknown maker';
            }


            if($key == 'Title') {
                $title = $metadatavalue;
            }

            if (!(isset($title))){
                $title = 'Unnamed item';
            }

        }
    }
}

?>

<div id="stc-section1" class="container-fluid">
    <h1 class="itemtitle hidden-sm hidden-xs"><?php echo $title .' / '. $maker. ' / '.$date;?></h1>
    <h4 class="itemtitle hidden-lg hidden-md"><?php echo $title .' / '. $maker. ' / '.$date;?></h4>
</div>

<?php
if (isset($solr[$link_uri_field]))
{
    foreach($solr[$link_uri_field] as $linkURI) {

        if (strpos($linkURI, 'luna') > 0) {
            //just for test, this line!
            //$tileSource = str_replace('images.is.ed.ac.uk', 'lac-luna-test2.is.ed.ac.uk:8181', $linkURI);
            $tileSource = str_replace('detail', 'iiif', $linkURI) . '/info.json';
            $iiifmax = str_replace('info.json', 'full/full/0/default.jpg', $tileSource);
            list($width, $height) = getimagesize($iiifmax);
            //echo 'WIDTH'.$width.'HEIGHT'.$height
            $portrait = true;
            if ($width > $height)
            {
                $portrait = false;
            }
            $json =  file_get_contents($tileSource);
            $jobj = json_decode($json, true);

            $error = json_last_error();

            $jsoncontext = $jobj['@context'];
            $jsonid = $jobj['@id'];
            $jsonheight = $jobj['height'];
            $jsonwidth = $jobj['width'];
            $jsonprotocol = $jobj['protocol'];
            $jsontiles = $jobj['tiles'];
            $jsonprofile = $jobj['profile'];
        }
    }
}
?>

<!--div id="stc-section2" class="container-fluid">
    <div class="col-lg-12 hidden-md hidden-sm hidden-xs main-image">
        <img class ="stc-img-responsive" src = "<?php
        if ($portrait){
            $iiifstatic = str_replace('info.json','full/,600/0/default.jpg',$tileSource);
        }
        else{
            $iiifstatic = str_replace('info.json','full/1200,/0/default.jpg',$tileSource);
        }
        echo $iiifstatic;?>">
    </div>
    <div class="col-md-9 hidden-lg hidden-sm hidden-xs resized-image">
        <img class ="stc-img-responsive" src = "<?php
        $iiifstatic = str_replace('info.json','full/!600,600/0/default.jpg',$tileSource);
        echo $iiifstatic;?>">
    </div>
    <div class="col-sm-6 hidden-lg hidden-md hidden-xs resized-image">
        <img class ="stc-img-responsive"  src = "<?php
        $iiifstatic = str_replace('info.json','full/!400,400/0/default.jpg',$tileSource);
        echo $iiifstatic;?>">
    </div>
    <div class="col-xs-3 hidden-lg hidden-md hidden-sm resized-image">
        <img class ="stc-img-responsive"  src = "<?php  $iiifstatic = str_replace('info.json','full/!200,200/0/default.jpg',$tileSource);
        echo $iiifstatic;
        ?>">
    </div>
</div-->

<div id="stc-section2" class="container-fluid">
    <div class="col-lg-12 hidden-md hidden-sm hidden-xs main-image">
        <div id="openseadragon">
            <script type="text/javascript">
                OpenSeadragon({
                    id: "openseadragon",
                    prefixUrl: "<?php echo base_url() ?>assets/openseadragon/images/",
                    tileSources: [{
                        "@context": "<?php echo $jsoncontext ?>",
                        "@id": "<?php echo $jsonid ?>",
                        "height": <?php echo $jsonheight ?>,
                        "width": <?php echo $jsonwidth ?>,
                        "profile":  [ "http://iiif.io/api/image/2/level2.json" ,
                            {
                                "formats" : [ "gif", "pdf"]
                            }
                        ],
                        "protocol": "<?php echo $jsonprotocol ?>",
                        "tiles": [{
                            "scaleFactors": [ 1, 2, 8, 16, 32 ],
                            "width": 512
                        }],
                        tileSize: 500,
                        //minLevel: 2,
                        preserveViewport: true,
                        visibilityRatio: 1,
                        minZoomLevel: 1,
                        defaultZoomLevel: 1,
                        sequenceMode: true
                    }]
                });
            </script>
        </div>
    </div>
    <div class="col-md-9 hidden-lg hidden-sm hidden-xs resized-image">
        <img class ="stc-img-responsive" src = "<?php
        $iiifstatic = str_replace('info.json','full/!600,600/0/default.jpg',$tileSource);
        echo $iiifstatic;?>">
    </div>
    <div class="col-sm-6 hidden-lg hidden-md hidden-xs resized-image">
        <img class ="stc-img-responsive"  src = "<?php
        $iiifstatic = str_replace('info.json','full/!400,400/0/default.jpg',$tileSource);
        echo $iiifstatic;?>">
    </div>
    <div class="col-xs-3 hidden-lg hidden-md hidden-sm resized-image">
        <img class ="stc-img-responsive"  src = "<?php  $iiifstatic = str_replace('info.json','full/!200,200/0/default.jpg',$tileSource);
        echo $iiifstatic;
        ?>">
    </div>


    <?php
    $numThumbnails = 0;
    $imageset = false;
    $thumbnailLink = array();
    if (isset($solr[$link_uri_field]))
    {
        foreach ($solr[$link_uri_field] as $linkURI)
        {
            if (strpos($linkURI, 'luna') > 0)
            {
                $tileSource = str_replace('detail', 'iiif', $linkURI) . '/info.json';
                $iiifmax = str_replace('info.json', 'full/full/0/default.jpg', $tileSource);
                list($width, $height) = getimagesize($iiifmax);
                $portrait = true;
                if ($width > $height)
                {
                    $portrait = false;
                }
                if ($portrait) {
                    $iiifurlsmall = str_replace('info.json', 'full/,250/0/default.jpg', $tileSource);
                }
                else{
                    $iiifurlsmall = str_replace('info.json', 'full/250,/0/default.jpg', $tileSource);
                }
                $iiifurlfull = str_replace('info.json', 'full/full/0/default.jpg', $tileSource);

                $thumbnailLink[$numThumbnails]  = '<a title = "' . $solr[$title_field][0] . '" class="fancybox" rel="group" href="' . $iiifurlfull . '"> ';
                $thumbnailLink[$numThumbnails] .= '<img src = "' . $iiifurlsmall . '" class="record-thumbnail-search" title="' . $solr[$title_field][0] . '" /></a>';

                $numThumbnails++;
                $imageset = true;
            }
        }
        if ($imageset == true) {
        //    echo $thumbnailLink[0];
        }
    }


?>





</div>


<div id="stc-section3" class="container-fluid">
    <!--h2 class="itemtitle hidden-sm hidden-xs">Categories</h2-->
    <!--h4 class="itemtitle hidden-md hidden-lg">Categories</h4-->
    <?php
    foreach($recorddisplay as $key) {

        $element = $this->skylight_utilities->getField($key);

        if(isset($solr[$element])) {

            foreach($solr[$element] as $index => $metadatavalue) {
                echo '<div class="stc-tags">';

                // if it's a facet search
                // make it a clickable search link
                if(in_array($key, $filters)) {
                    if (!strpos($metadatavalue, "/")> 0)
                    {
                        $orig_filter = urlencode($metadatavalue);
                        $lower_orig_filter = strtolower($metadatavalue);
                        $lower_orig_filter = urlencode($lower_orig_filter);

                        echo '<a href="./search/*:*/' . $key . ':%22' . $lower_orig_filter . '%7C%7C%7C' . $orig_filter . '%22">' . $metadatavalue . '</a>';
                    }
                }
                echo '</div>';

            }
        }
    }
    ?>
</div>
<?php

if(isset($solr[$bitstream_field]) && $link_bitstream) {

    //if (!$videoLink == '' or !$audioLink == '')
    if (!$audioLink == '')
    {
        echo '<div id="stc-section4" class="container-fluid">
            <!--h1 class="itemtitle hidden-sm hidden-xs">Audio/Visual</h1-->
            <!--h4 class="itemtitle hidden-lg hidden-md">Audio/Visual</h4-->'.
            $audioLink . '</div>';
    }
}
?>


        <div id="stc-section5" class="panel panel-default container-fluid">
            <div class="panel-heading">
                <h2 class="panel-title hidden-sm hidden-xs ">
                    <a class="accordion-toggle" data-toggle="collapse" href="#collapse1">More Data</a>
                </h2>
                <h4 class="panel-title hidden-md hidden-lg ">
                    <a class="accordion-toggle" data-toggle="collapse" href="#collapse1">More Data</a>
                </h4>
            </div>
            <div id="collapse1" class="panel-collapse collapse">
                <div class="panel-body">
                    <div class="col-sm-6 col-xs-12 col-md-8 col-lg-12 metadata">
                    <dl class="dl-horizontal">
                    <?php

                        foreach($recorddisplay as $key) {
                        $element = $this->skylight_utilities->getField($key);

                            if (isset($solr[$element])) {
                                foreach ($solr[$element] as $index => $metadatavalue) { ?>

                                    <?php echo '<dt>' . $key . '</dt>';
                                          echo '<dd>' . $metadatavalue . '</dd>';

                                }
                            }
                        }
                    ?>

                    </dl>
                    </div>
                </div> <!-- panel body -->
            </div>
        </div>



</div>