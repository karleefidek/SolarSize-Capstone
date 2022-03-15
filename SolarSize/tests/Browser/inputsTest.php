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
                    ->keys('#timeZone', '-6')
					->assertValue('#timeZone','-6')
                    ->keys('#vs2__combobox > div.vs__selected-options > input', 'Optimized', '{ENTER}')
					->keys('#vs1__combobox > div.vs__selected-options > input','Test','{ENTER}')
					->assertValue('#timeZone','-6')	
                    ->click('#mainForm > div:nth-child(1) > div > div > div > div:nth-child(6) > div.vue2leaflet-map.leaflet-container.leaflet-touch.leaflet-fade-anim.leaflet-grab.leaflet-touch-drag.leaflet-touch-zoom')
					->assertValueIsNot('#latitude','0')
					->assertValueIsNot('#longitude','0')
					->screenshot('summaryTest1')
					->click('#mainForm > div.submit-container > button')
					->waitFor('#app > div.extra > div:nth-child(2) > div:nth-child(1) > div:nth-child(1) > div > main > div.roiInputs', 30)
					->assertSee('100.00');
			$browser->scrollIntoView('#app > div.extra > div:nth-child(2) > div:nth-child(1) > div:nth-child(1) > div > header > h3')
					->screenshot('summaryTest2');
        });
    }
}
