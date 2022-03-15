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
					->screenshot('summaryTest1')
					->click('#mainForm > div.submit-container > button')
					->waitFor('#roiValue', 30)
					->assertSee('100.00');
			$browser->scrollIntoView('#app > div.extra > div:nth-child(2) > div:nth-child(1) > div:nth-child(1) > div > header > h3')
					->screenshot('summaryTest2');
        });
    }
}
