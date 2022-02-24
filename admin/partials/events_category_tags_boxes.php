<?php

/**
 * Get custom boxes for categoris and tags.
 */
function get_category_box() {

	global $post;
	$emp_category       = get_post_meta( $post->ID, 'emp_category', true );
	$emp_category_array = explode( ',', $emp_category );

	$emp_tags       = get_post_meta( $post->ID, 'emp_tags', true );
	$emp_tags_array = explode( ',', $emp_tags );
	?>
	<div class="dffmain_category_tags_section">
		<div class="dffmain_category_block">
			<h4>
				Select Categories
			</h4>
			<ul id="events_categorieschecklist" class="categorychecklist form-no-clear">
				<?php
				$cat_args = array(
					'taxonomy'    => 'events_categories',
					'orderby'    => 'term_id',
					'order'      => 'ASC',
					'hide_empty' => 0,
					'parent'     => '0',
				);
				$terms = get_terms( $cat_args );
				if ( isset( $terms ) && ! empty( $terms ) ) {
					foreach ( $terms as $terms_data ) {
						$cat_id = $terms_data->term_id;
						?>
						<li>
							<label class="post_type_lable" for="<?php echo esc_attr( $terms_data->slug ); ?>">
								<input 
									name="emp_category[]" 
									type="checkbox"
									id="<?php echo esc_attr( $terms_data->slug ); ?>"
									value="<?php echo esc_attr( $terms_data->term_id ); ?>" 
									<?php if ( in_array( (string) $terms_data->term_id, $emp_category_array, true ) ) echo 'checked'; ?>
								>
								<?php echo esc_attr( $terms_data->name ); ?>
							</label>
						</li>
						<?php
						$get_children_cats = array(
							'taxonomy'    => 'events_categories',
							'orderby'    => 'term_id',
							'order'      => 'ASC',
							'parent'     => $cat_id,
							'hide_empty' => 0,
						);
						$child_cats = get_terms( $get_children_cats );
						?>
						<ul class="event_child_category" id="event_child_<?php echo esc_attr( $terms_data->term_id ); ?>">
							<?php
							if ( isset( $child_cats ) && ! empty( $child_cats ) ) {
								foreach ( $child_cats as $child_cats_data ) {
									$child_catid = $child_cats_data->term_id;
									?>
									<li>
										<label class="post_type_lable" for="<?php echo esc_attr( $child_cats_data->slug ); ?>">
											<input
												name="emp_category[]" type="checkbox"
												id="<?php echo esc_attr( $child_cats_data->slug ); ?>"
												value="<?php echo esc_attr( $child_cats_data->term_id ); ?>" 
												<?php if ( in_array( (string) $child_cats_data->term_id, $emp_category_array, true ) ) echo 'checked'; ?>
											>
											<?php echo esc_attr( $child_cats_data->name ); ?>
										</label>
									</li>
									<?php
									$get_super_children_cats = array(
										'taxonomy'    => 'events_categories',
										'orderby'    => 'term_id',
										'order'      => 'ASC',
										'parent'     => $child_catid,
										'hide_empty' => 0,
									);
									$super_child_cats = get_terms($get_super_children_cats );
									?>
									<ul class="event_super_child_category" id="event_child_<?php echo esc_attr( $child_cats_data->term_id ); ?>">
										<?php
										if ( isset( $super_child_cats ) && ! empty( $super_child_cats ) ) {
											foreach ( $super_child_cats as $super_child_cats_data ) {
												?>
												<li>
													<label class="post_type_lable" for="<?php echo esc_attr( $super_child_cats_data->slug ); ?>">
													<input
														name="emp_category[]" type="checkbox"
														id="<?php echo esc_attr( $super_child_cats_data->slug ); ?>" 
														value="<?php echo esc_attr( $super_child_cats_data->term_id ); ?>" 
														<?php if ( in_array( (string) $super_child_cats_data->term_id, $emp_category_array, true ) ) echo 'checked'; ?>
													>
														<?php echo esc_attr( $super_child_cats_data->name ); ?>
													</label>
												</li>
												<?php
											}/**foreach ( $super_child_cats as $super_child_cats_data ) { */
										}/**if ( isset( $super_child_cats ) && ! empty( $super_child_cats ) ) { */
										?>
									</ul>
									<?php	
								}/**foreach ( $child_cats as $child_cats_data ) { */
							}/**if ( isset( $child_cats ) && ! empty( $child_cats ) ) { */
							?>
						</ul>
						<?php
					}/**foreach ( $terms as $terms_data ) { */
				}/**if ( isset( $terms ) && ! empty( $terms ) ) */
				?>
			</ul>

			<div id="events_categories-adder" class="wp-hidden-children">
				<a id="events_categories-add-toggle" href="javascript:void(0)"class="hide-if-no-js taxonomy-add-new-cat">
					+ Add New Category
				</a>
				<p id="events_categories-add" class="category-add wp-hidden-child">
					<label class="screen-reader-text" for="newevents_categories">
						Add New Category
					</label>
					<input 
						type="text" name="newevents_categories" 
						id="newevents_categories"
						class="form-required" 
						placeholder="New Category" 
						aria-required="true"
					>
					<label class="screen-reader-text" for="newevents_categories_parent">
						Parent Topic:
					</label>
					<select class="postform" name="newevents_categories_parent" id="newevents_categories_parent">
						<option value="-1">— Parent Category —</option>
						<?php
						foreach ( $terms as $terms_data ) {
							$cat_id = $terms_data->term_id;
							?>
							<option class="level-0" value="<?php echo esc_attr( $cat_id ); ?>">
								<?php echo esc_html( $terms_data->name ); ?>
							</option>
							<?php
							$get_children_cats = array(
								'taxonomy'    => 'events_categories',
								'orderby'    => 'term_id',
								'order'      => 'ASC',
								'parent'     => $cat_id,
								'hide_empty' => 0,
							);
							$child_cats = get_terms( $get_children_cats );
							if ( isset( $child_cats ) && ! empty( $child_cats ) ) {
								foreach ( $child_cats as $child_cats_data ) {
									$child_catid = $child_cats_data->term_id;
									?>
									<option class="level-1" value="<?php echo esc_attr( $child_catid ); ?>">
										&nbsp;&nbsp;&nbsp;<?php echo esc_html( $child_cats_data->name ); ?>
									</option>
									<?php
									$get_super_children_cats = array(
										'taxonomy'    => 'events_categories',
										'orderby'    => 'term_id',
										'order'      => 'ASC',
										'parent'     => $child_catid,
										'hide_empty' => 0,
									);
									$super_child_cats = get_terms( $get_super_children_cats );
									if ( isset( $super_child_cats ) && ! empty( $super_child_cats ) ) {
										foreach ( $super_child_cats as $super_child_cats_data ) {
											?>
											<option class="level-2" value="<?php echo esc_attr( $super_child_cats_data->term_id ); ?>">
												&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo esc_attr( $super_child_cats_data->name ); ?>
											</option>
											<?php
										}
									}
								}/**foreach ( $child_cats as $child_cats_data ) { */
							}/**if ( isset( $child_cats ) && ! empty( $child_cats ) ) { */
						}/**foreach ( $terms as $terms_data ) { */
						?>
					</select>
					<input 
						type="button" 
						id="events_categories-add-submit"
						data-wp-lists="add:events_categorieschecklist:events_categories-add"
						class="button dffmain-category-add-submit" 
						value="Add New Category"
					>
				</p>
			</div>
		</div>

		<div class="english_tags_block">
			<h4>
				Select Tags
			</h4>
			<ul id="events_tagsschecklist" class="tagschecklist form-no-clear">
				<?php
				$cat_args = array(
					'taxonomy'   => 'events_tags',
					'orderby'    => 'term_id',
					'order'      => 'ASC',
					'hide_empty' => 0,
					'parent'     => '0',
				);
				$terms = get_terms( $cat_args );
				if ( isset( $terms ) && ! empty( $terms ) ) {
					foreach ( $terms as $terms_data ) {
						?>
						<li>
							<label class="post_type_lable" for="<?php echo esc_attr( $terms_data->term_id ); ?>">
								<input
									name="emp_tags[]" 
									type="checkbox"
									id="<?php echo esc_attr( $terms_data->term_id ); ?>"
									value="<?php echo esc_attr( $terms_data->term_id ); ?>" 
									<?php if ( in_array( (string) $terms_data->term_id, $emp_tags_array, true ) ) echo 'checked'; ?>
								>
								<?php echo esc_attr( $terms_data->name ); ?>
							</label>
						</li>
						<?php
					}
				}
				?>
			</ul>
			<div id="events_tags-adder" class="wp-hidden-children">
				<a id="events_tags-add-toggle" href="javascript:void(0)" class="hide-if-no-js taxonomy-add-new-tags">
				   + Add New Tag
				</a>
				<p id="events_tags-add" class="category-add wp-hidden-child">
					<label class="screen-reader-text" for="newevents_tags">
						Add New Tag
					</label>
					<input 
						type="text" 
						name="newevents_tags" 
						id="newevents_tags" 
						class="form-required"
						placeholder="New Tag" 
						aria-required="true"
					>
					<input 
						type="button" 
						id="events_tags-add-submit"
						data-wp-lists="add:events_tagschecklist:events_tags-add"
						class="button dffmain-tags-add-submit" 
						value="Add New Tag"
					>
				</p>
			</div>
		</div>

	</div>
	<?php
}