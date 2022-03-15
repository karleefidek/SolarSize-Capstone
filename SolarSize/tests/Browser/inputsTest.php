<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class inputsTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function testExample()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')			               
					->keys('#vs1__combobox > div.vs__selected-options > input','Test','{ENTER}')
                    ->click('#mapSelect')
					->assertValueIsNot('#latitude','0')
					->assertValueIsNot('#longitude','0')
					->attach('#mainForm > div:nth-child(1) > div > div > div > div:nth-child(4) > div > div > input', '/home/vagrant/code/tests/Browser/monthdata_cleaned.csv')
					->screenshot('summaryTest1')
					->click('#mainForm > div.submit-container > button')
					->waitFor('#roiValue', 30)
					->assertSee('219');
			$browser->scrollIntoView('#app > div.extra > div:nth-child(2) > div:nth-child(1) > div:nth-child(1) > div > header > h3')
					->screenshot('summaryTest2');
        });
    }
}
