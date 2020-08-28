<?php

WP_CLI::add_command( 'pba', 'PBA_CLI' );

/**
 * Filter spam comments.
 */
class PBA_CLI extends WP_CLI_Command {

	/**
	 * Test command
	 *
	 * ## EXAMPLES
	 *
	 *     wp pba test
	 *
	 */
	public function test() {
		WP_CLI::line( 'Test.' );
	}
}