jQuery(document).ready(function() {
	function create_checkall(parent_selector,child_selector,surrounding_element) {
		// Parent actions: 
		jQuery(parent_selector).click(function() {
			// clicking the parent checkbox should check or uncheck all child checkboxes
			jQuery(this).parents(surrounding_element + ':eq(0)').find(child_selector).attr('checked', this.checked);
		});
		// Child actions:
		jQuery(child_selector).click(function() {
			// unchecking any child should uncheck the parent checkbox
			if (this.checked == false || jQuery(this).parents(surrounding_element + ':eq(0)').find(parent_selector).attr('checked') == true && this.checked == false) {
				jQuery(this).parents(surrounding_element + ':eq(0)').find(parent_selector).attr('checked', false);
			}
			// if a child is checked, see if ALL children are checked and if so, check the parent
			else if (this.checked == true) {
				var flag = true;
				jQuery(this).parents(surrounding_element + ':eq(0)').find(child_selector).each(function() {
					if (this.checked == false)
					flag = false;
				});
				jQuery(this).parents(surrounding_element + ':eq(0)').find(parent_selector).attr('checked', flag);
			}
		});
	}
	// set up our checkboxes
	create_checkall('.parent_check','.child_check','tr');
	create_checkall('.master_check','.child_check,.parent_check','table');
});