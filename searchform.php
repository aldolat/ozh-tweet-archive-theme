<form method="get" id="searchform" action="<?php echo esc_url( home_url( '/' ) ); ?>">
  <input type="text" name="s" id="s" value="<?php the_search_query(); ?>" class="txt" /><input type="submit" id="searchsubmit" value="&nbsp;" class="btn" />
</form>