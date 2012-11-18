<ul id="social_links">
 <li>
   <a class="facebook" rel="nofollow" href="http://www.facebook.com/sharer.php?u=<?php the_permalink();?>&amp;t=<?php echo urlencode(get_the_title($id)); ?>" title="Share this post on Facebook" target="_blank">
    <i class="icon-facebook-sign"></i>

   </a>
 </li>
 <li>
   <a class="twitter" rel="nofollow" href="http://twitter.com/home?status=<?php echo urlencode("Currently reading: "); ?><?php the_permalink(); ?>" title="Share this article with your Twitter followers" target="_blank">
     <i class="icon-twitter-sign"></i>

   </a>
 </li>
 <li>
   <a class="email" href="mailto:type email address here?subject=I wanted to share this post with you from <?php bloginfo('name'); ?>&body=<?php the_title('','',true); ?>&#32;&#32;<?php the_permalink(); ?>" title="Email to a friend/colleague" target="_blank">
     <i class="icon-envelope-alt"></i>

   </a>
 </li>
</ul> 