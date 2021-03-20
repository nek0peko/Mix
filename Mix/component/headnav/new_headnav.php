<?php $this->need('component/loader.php');
?>
<style>
@media all and (max-width:600px){
  #headnav-a{
    color: #664238!important;
  }
}
@media all and (min-width:600px){
    #btn_active, #btn_sidebar{ display: none!important;}

}
#headnav-a{
    color: #664238;
  }

  @media(min-width: 898px){
    header{
        display: flex;
        justify-content: space-between;
    }
    .head-logo{
        margin-left: 150px;
    }
    .Header_head-swiper__37Fg8 {
        margin-right: 150px;
    }
}
.head-logo{
    background: transparent!important;
    left: 100px;
}
.head-logo svg {
    height: 2.5em;
}
.Header_title__1THMF {
    display: inline-block;
    font-weight: 500;
    font-size: 1.25rem;
    line-height: 1.75rem;
    margin-top: 0;
    margin-left: 0.5rem;
    position: relative;
    bottom: 7px;
}

.head-btn{
    cursor: pointer;
    display: block;
    padding-top: 1rem;
    padding-bottom: 1rem;
    padding-left: 1rem;
    position: absolute;
    right: 0;
    margin-right: 10px;
    visibility: visible;
    top: 50%;
    -webkit-transform: translateY(-50%);
    transform: translateY(-50%);
    padding: 6px;
}
</style>
<header class="assets" id="header">
    <div class="jsx-685620294 head-btn" id="<?php if ($this->options->sideBarStyle == 1):?>btn_active<?php elseif($this->options->sideBarStyle == 2):?>btn_sidebar<?php endif;?>" style="display: flex; align-items: center; justify-content: center; padding: 1em; cursor: pointer;"><svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="list-ul" class="svg-inline--fa fa-list-ul fa-w-16 " role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M48 48a48 48 0 1 0 48 48 48 48 0 0 0-48-48zm0 160a48 48 0 1 0 48 48 48 48 0 0 0-48-48zm0 160a48 48 0 1 0 48 48 48 48 0 0 0-48-48zm448 16H176a16 16 0 0 0-16 16v32a16 16 0 0 0 16 16h320a16 16 0 0 0 16-16v-32a16 16 0 0 0-16-16zm0-320H176a16 16 0 0 0-16 16v32a16 16 0 0 0 16 16h320a16 16 0 0 0 16-16V80a16 16 0 0 0-16-16zm0 160H176a16 16 0 0 0-16 16v32a16 16 0 0 0 16 16h320a16 16 0 0 0 16-16v-32a16 16 0 0 0-16-16z"></path></svg></div>
    
        <div class="assets head-logo"><a id="headnav-a" href="<?php Helper::options()->siteUrl()?>">
        
        <?php if($this->options->HeadNavPhoto): ?>
        <?php $this->options->HeadNavPhoto(); ?><h1 class="Header_title__1THMF Header_title__EwaWq global-title"><?php $this->options->title(); ?></h1>
        <?php else: ?>
<svg height="200px" viewBox="0 0 200 200" version="1.1"><desc>Created with Sketch.</desc><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><g id="forest" fill="currentColor" fill-rule="nonzero"><path d="M116.799219,176.953125 C112.484375,176.953125 108.986719,180.450781 108.986719,184.765625 L108.986719,192.1875 C108.986719,196.502344 112.484375,200 116.799219,200 C121.114062,200 124.611719,196.502344 124.611719,192.1875 L124.611719,184.765625 C124.611719,180.450781 121.114063,176.953125 116.799219,176.953125 Z" id="Path"></path><path d="M121.456641,150.248047 C121.298828,150.158203 108.089453,142.472656 101.778125,128.767188 C106.058594,127.733594 109.462109,126.055078 112.442578,124.584766 C115.008203,123.319531 116.671094,120.749609 116.787109,117.891406 C116.903125,115.033203 115.441797,112.336328 112.986719,110.867578 C108.091406,107.937891 96.6117187,99.3179688 91.234375,88.9808594 C94.3324219,88.1222656 97.3414062,86.940625 100.255469,85.734375 C102.819922,84.6730469 104.628906,82.3304688 105.007422,79.5808594 C105.385938,76.83125 104.276953,74.0875 102.094531,72.3722656 C82.3632812,56.8691406 70.0417969,20.1820313 69.9210937,19.8171875 C68.8640625,16.6136719 65.8710938,14.4558594 62.5019531,14.4558594 C62.4839844,14.4558594 62.465625,14.4558594 62.4472656,14.4558594 C59.05625,14.4792969 56.0664063,16.690625 55.0484375,19.925 C54.934375,20.2878906 43.3839844,56.3890625 22.3355469,71.8246094 C20.075,73.4824219 18.8757813,76.2242188 19.1933594,79.0097656 C19.5109375,81.7949219 21.2964844,84.196875 23.8722656,85.303125 C26.1265625,86.2714844 29.5851563,87.7554688 34.3464844,89.0640625 C29.0214844,98.8472656 17.2527344,107.819922 12.2683594,110.952734 C9.7640625,112.517969 8.35625,115.360938 8.63007813,118.301563 C8.90390625,121.242188 10.8117188,123.776953 13.5617188,124.852734 C13.9058594,124.9875 14.2785156,125.1375 14.6828125,125.3 C16.8089844,126.154297 19.7445313,127.332031 23.6902344,128.501953 C17.2066406,141.350391 3.68632813,150.395703 3.53671875,150.494141 C1.09140625,152.094922 -0.25625,154.926563 0.044140625,157.833594 C0.34453125,160.740625 2.2421875,163.237109 4.96328125,164.303906 C5.76640625,164.61875 23.6132813,171.474609 55.471875,172.521875 L55.471875,192.1875 C55.471875,196.502344 58.9695313,200 63.284375,200 C67.5992187,200 71.096875,196.502344 71.096875,192.1875 L71.096875,172.544531 C103.353125,171.572656 119.956641,164.517578 120.696484,164.196094 C123.407422,163.016797 125.221094,160.408984 125.382422,157.457031 C125.542578,154.504688 124.023438,151.714844 121.456641,150.248047 Z M63.6742188,157.03125 C46.6414063,157.03125 33.3425781,155.194922 24.325,153.367578 C29.7734375,147.926953 35.75625,140.533203 39.3648438,131.730859 C44.5152344,132.389453 50.4929688,132.8125 57.4242188,132.8125 C61.7390625,132.8125 65.2367188,129.314844 65.2367188,125 C65.2367188,120.685156 61.7390625,117.1875 57.4242188,117.1875 C46.7472656,117.1875 38.8265625,116.116406 32.8878906,114.769531 C33.4640625,114.256641 34.0460937,113.726953 34.63125,113.180859 C42.2988281,106.020703 47.5574219,98.8085938 50.3394531,91.6605469 C51.3,91.7195313 52.2839844,91.7652344 53.2945313,91.7941406 C57.6179688,91.9210938 61.2035156,88.5207031 61.3269531,84.2082031 C61.45,79.8949219 58.0535156,76.2988281 53.740625,76.1757813 C49.2144531,76.0464844 45.3546875,75.5386719 42.0863281,74.8628906 C51.1160156,64.9648438 57.9273437,52.8929688 62.5585937,43.0527344 C67.1378906,52.8117188 73.746875,64.7949219 82.1886719,74.6710938 C81.9644531,74.6675781 81.74375,74.6566406 81.5277344,74.6382813 C79.1414062,74.4355469 76.7910156,75.3363281 75.1546875,77.0871094 C73.5183594,78.8375 72.7753906,81.2421875 73.1398437,83.6105469 C75.0921875,96.3007813 83.7679687,106.876563 91.5785156,114.065234 C89.2605469,114.0125 87.06875,114.98125 85.5433594,116.714844 C83.9519531,118.523438 83.2839844,120.964844 83.7328125,123.33125 C86.2589844,136.650391 93.8433594,146.823047 100.754297,153.641797 C92.2726562,155.367188 79.8890625,157.03125 63.6742188,157.03125 Z" id="Shape"></path><path d="M199.920703,162.187891 C199.623438,160.078516 198.473047,158.178125 196.74375,156.933203 C196.677734,156.885547 190.046094,152.078516 183.367188,144.459375 C178.680469,139.112891 175.16875,133.808203 172.869141,128.615234 C177.176953,127.299219 180.334766,125.896484 182.855078,124.776563 C183.779688,124.365625 184.578125,124.011328 185.239063,123.752344 C187.985938,122.677344 189.889453,120.144141 190.166016,117.207422 C190.442578,114.270703 189.037891,111.426172 186.539844,109.857031 C180.508203,106.06875 165.928516,94.9003906 160.170313,82.8882813 C165.975,81.6109375 170.135938,80.0996094 173.450781,78.6757813 C176.026953,77.5691406 177.8125,75.1675781 178.130078,72.3824219 C178.447656,69.596875 177.248438,66.8550781 174.987891,65.1972656 C150.69375,47.3816406 137.426172,5.89023438 137.296484,5.47773438 C136.282422,2.23945312 133.294141,0.02578125 129.900781,0 C129.880469,0 129.860938,0 129.840625,0 C126.471875,0 123.479297,2.1609375 122.422266,5.36484375 C122.345703,5.59726563 114.594141,28.8386719 101.137891,48.2902344 C98.6832031,51.8386719 99.5699219,56.7050781 103.118359,59.1597656 C106.666016,61.6144531 111.532813,60.7277344 113.987891,57.1796875 C120.606641,47.6113281 125.908984,37.3730469 129.771094,28.9074219 C135.158203,40.7210938 143.499219,55.9507812 154.835547,68.0292969 C148.182031,69.3046875 138.466406,70.365625 124.221094,70.365625 C119.90625,70.365625 116.408594,73.8632812 116.408594,78.178125 C116.408594,82.4929687 119.90625,85.990625 124.221094,85.990625 C131.989844,85.990625 138.586719,85.7003906 144.237891,85.2101562 C147.194141,93.6328125 153.265625,102.16875 162.363672,110.664453 C163.722266,111.933203 165.067578,113.123437 166.365234,114.225781 C160.423047,115.830469 152.078516,117.127344 139.455469,117.127344 C135.140625,117.127344 131.642969,120.625 131.642969,124.939844 C131.642969,129.254687 135.140625,132.752344 139.455469,132.752344 C146.397656,132.752344 152.275391,132.379297 157.298828,131.778516 C160.196094,139.490625 164.986719,147.194531 171.617578,154.758984 C173.749219,157.191406 175.869141,159.373437 177.836328,161.269531 C171.480469,162.880469 160.966797,164.453125 144.142969,164.453125 C139.828125,164.453125 136.330469,167.950781 136.330469,172.265625 L136.330469,192.1875 C136.330469,196.502344 139.828125,200 144.142969,200 C148.457813,200 151.955469,196.502344 151.955469,192.1875 L151.955469,179.966016 C166.112109,179.546484 177.524219,177.948828 185.952344,175.201562 C193.906641,172.608203 197.119922,169.648438 198.327734,168.114453 C199.645703,166.441016 200.217969,164.297266 199.920703,162.187891 Z" id="Path"></path></g></g></svg>
<h1 class="Header_title__1THMF Header_title__EwaWq global-title"><?php $this->options->title(); ?></h1>
          <?php endif; ?>
        
        
        </a></div>
        <div class="assets Header_head-swiper__37Fg8">
          <nav class="assets Header_head-info__17tgL">
            <div class="assets ">
              <div class="assets Header_meta__YYzbP"></div>
              <div class="assets Header_title__EwaWq"></div>
            </div>
          </nav>
          <nav class="assets head-menu Header_head-menu__ofiV5" id="Header_head-menu__ofiV5">
            <div class="has-child"><a href="<?php Helper::options()->siteUrl()?>"><i class="fa fa-dot-circle"></i><span>源</span></a>
                <div class="sub-menu">
                <?php $this->widget('Widget_Contents_Page_List')->to($pages); ?>
                <?php while($pages->next()): ?>
                  <a href="<?php $pages->permalink(); ?>"><span><?php $pages->title(); ?></span></a>
                <?php endwhile; ?>
                </div>
            </div>
            <div class="has-child"><a href="#"><i class="fa fa-book"></i><span>文</span></a>
                <?php $this->widget('Widget_Metas_Category_List')->to($category); ?>
                <?php if($category->have()): ?>
                  <div class="sub-menu">
                <?php while ($category->next()): ?>
                  <a href="<?php $category->permalink(); ?>"><span><?php $category->name(); ?></span></a>
                <?php endwhile; ?>
                <?php endif; ?>
            </div>
            </div>
            <!--
            <div class="menu-link"><a href="#"><i class="fa fa-feather-alt"></i><span>记</span></a></div>
            <div class="menu-link"><a href="#"><i class="fa fa-comments"></i><span>言</span></a></div>
            <div class="has-child"><a href="#"><i class="fa fa-history"></i><span>览</span></a>
              <div class="sub-menu"><a href="#"><i class="fa fa-feather-alt"></i><span>生活</span></a><a href="#"><i class="fa fa-book-open"></i><span>博文</span></a></div>
            </div>   -->
            <div class="menu-link"><a href="<?php $this->options->FriendURL();?>"><i class="fa fa-users"></i><span>友</span></a></div>
                        <?php
              $hideHomeItem = false;
              if(!empty(Typecho_Widget::widget('Widget_Options')->headnavItems)){
                  $json = '['.Utils::remove_last_comma(Typecho_Widget::widget('Widget_Options')->headnavItems).']';
                  
                  $headnavItems = json_decode($json);
                  $headnavItemsOutput = "";
                  foreach ($headnavItems as $headnavItem){

                      @$itemName = $headnavItem->name;
                      @$itemStatus = $headnavItem->status;

                      @$itemSub = $headnavItem->sub;

                      if ($itemName === 'home' && strtoupper($itemStatus) === 'HIDE'){
                          $hideHomeItem = true;
                          continue;//本次循环结束，不再执行下面内容
                      }

                      $haveSub = false;
                      $subListHtml = "";
//                      print_r($itemSub);
                      if (is_array($itemSub)){
                          $haveSub = true;
                          $subListHtml .= '<div class="sub-menu">';
                          foreach ($itemSub as $subItem){
                              $subListHtml .= Content::returnHeadItem($subItem,false,"" ,'yes');
                          }
                          $subListHtml .= '</div>';
                      }

                      $headnavItemsOutput .= Content::returnHeadItem($headnavItem,$haveSub,$subListHtml);
                  }
              }
              ?>
              <?php echo @$headnavItemsOutput;?>
          </nav>
        </div>
              </header>