<?php

WP_CLI::add_command( 'jddj', 'JDDJ_CLI' );

/**
 * Filter spam comments.
 */
class JDDJ_CLI extends WP_CLI_Command {

	/**
	 * Download all cpc history reviews.
	 *
	 * ## EXAMPLES
	 *
	 *     wp jddj download-review
	 *
	 */
	public function download_review() {

		WP_CLI::line( 'Test result.' );

	}
}