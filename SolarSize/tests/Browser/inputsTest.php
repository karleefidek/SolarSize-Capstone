<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class inputsTest extends DuskTestCase
{
    /**
     * Testing the panel calculations
     *
     * 
     */
	  /**
     * @group testing1
     */
    public function testLongi360Panel()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
					->keys('#vs1__combobox > div.vs__selected-options > input','Test','{ENTER}')
					->keys('#grant','00')
					->keys('#interest','{backspace}','{backspace}','5')
	 				->assertValueIsNot('#latitude','0')
					->assertValueIsNot('#longitude','0')
					->attach("#fileUpload > div > input", '/home/vagrant/code/tests/Browser/monthdata_cleaned.csv')
					->screenshot('panelTest1') 
					->click('#mainForm > div.submit-container > button.submit-button')
					->screenshot('panelTest1.1')
					->waitForText('Return Statistics',30)
					->scrollIntoView('#app > div.extra > div:nth-child(2) > div:nth-child(1) > div:nth-child(1) > div > header > h3')
					->screenshot('panelTest1.2')
					->assertSee('317796'); 
			$browser->driver->manage()->deleteAllCookies();
        });
    }
	/**
    * @group testing1
    */
	public function testLongi450Panel()
    {
        $this->browse(function (Browser $browser1) {
            $browser1->visit('/')			               
					->keys('#vs1__combobox > div.vs__selected-options > input','Test','{ENTER}')
					->keys('#grant','00')
					->keys('#interest','{backspace}','{backspace}','5')
					->keys('#panel_eff2','{backspace}','{backspace}','{backspace}','4')
	 				->assertValueIsNot('#latitude','0')
					->assertValueIsNot('#longitude','0')
					->attach("#fileUpload > div > input", '/home/vagrant/code/tests/Browser/monthdata_cleaned.csv')
					->screenshot('panelTest2') 
					->click('#mainForm > div.submit-container > button.submit-button')
					->screenshot('panelTest2.1')
					->waitForText('Return Statistics',30)
					->scrollIntoView('#app > div.extra > div:nth-child(2) > div:nth-child(1) > div:nth-child(1) > div > header > h3')
					->screenshot('panelTest2.2')
					->assertSee('793072'); 
			$browser1->driver->manage()->deleteAllCookies();
        });
    }
	/**
    * @group testing2
    */
	public function test310BlackFrame()
    {
        $this->browse(function (Browser $browser1) {
            $browser1->visit('/')			                 
					->keys('#vs1__combobox > div.vs__selected-options > input','Test','{ENTER}')
					->keys('#grant','0')
					->keys('#panel_eff0','{backspace}','{backspace}','{backspace}','4')
					->keys('#interest','{backspace}','{backspace}','2')
	 				->assertValueIsNot('#latitude','0')	
					->assertValueIsNot('#longitude','0')
					->attach("#fileUpload > div > input", '/home/vagrant/code/tests/Browser/monthdata_cleaned.csv')
					->screenshot('panelTest1') 
					->click('#mainForm > div.submit-container > button.submit-button')
					->screenshot('panelTest1.1')
					->waitForText('Return Statistics',30)
					->scrollIntoView('#app > div.extra > div:nth-child(2) > div:nth-child(1) > div:nth-child(1) > div > header > h3')
					->screenshot('panelTest1.2')
					->assertSee('768046'); 
			$browser1->driver->manage()->deleteAllCookies();
        }); 
	}

	
	/**
     * Testing inputs with good values, error values, and edge cases
     *
     * 
     */
	public function testErrorLatitude()
	{
		$this->browse(function (Browser $browser) {
            $browser->visit('/')
					->keys('#latitude','235')
					->assertSee('Invalid latitude - must be between -90 and 90')
					->keys('#latitude','{backspace}','{backspace}','{backspace}')
					->keys('#latitude','-95')
					->assertSee('Invalid latitude - must be between -90 and 90')
					->keys('#latitude','{backspace}','{backspace}','{backspace}')
					->keys('#latitude','er')
					->assertSee('Invalid latitude - must be between -90 and 90');
	});
	}
	
	public function testGoodLatitude()
	{
		$this->browse(function (Browser $browser) {
            $browser->visit('/')
					->keys('#latitude','23')
					->assertDontSee('Invalid latitude - must be between -90 and 90');
	});
	}
	
	public function testErrorLongitude()
	{
		$this->browse(function (Browser $browser) {
            $browser->visit('/')
					->keys('#longitude','235')
					->assertSee('Invalid longitude - must be between -180 and 180')
					->keys('#longitude','{backspace}','{backspace}','{backspace}')
					->keys('#longitude','er')
					->assertSee('Invalid longitude - must be between -180 and 180')
					->keys('#longitude','{backspace}','{backspace}')
					->keys('#longitude','-190')
					->assertSee('Invalid longitude - must be between -180 and 180');
	});
	}
	
	public function testGoodLongitude()
	{
		$this->browse(function (Browser $browser) {
            $browser->visit('/')
					->keys('#longitude','23')
					->assertDontSee('Invalid longitude - must be between -180 and 180')
					->keys('#longitude','{backspace}','{backspace}')
					->keys('#longitude','2')
					->screenshot('summaryTest13')
					->assertDontSee('Invalid longitude - must be between -180 and 180');
	});
	}
	
	
	public function testErrorRoofArea()
	{
		$this->browse(function (Browser $browser) {
            $browser->visit('/')
					->keys('#area','ew')
					->assertSee('Invalid loss coefficient - must be atleast room for 1 panel')
					->keys('#area','{backspace}','{backspace}')
					->keys('#area','-2')
					->assertSee('Invalid loss coefficient - must be atleast room for 1 panel');
	});
	}
	
	public function testGoodRoofArea()
	{
		$this->browse(function (Browser $browser) {
            $browser->visit('/')
					->keys('#area','400')
					->assertDontSee('Invalid loss coefficient - must be atleast room for 1 panel');				
	});
	}
	
	public function testErrorGrant()
	{
		$this->browse(function (Browser $browser) {
            $browser->visit('/')
					->keys('#grant','-5')
					->assertSee('Invalid grant cost - must be greater than positive')
					->keys('#grant','{backspace}','{backspace}')
					->keys('#grant','er')
					->assertSee('Invalid grant cost - must be greater than positive');
	});
	}
	
	public function testGoodGrant()
	{
		$this->browse(function (Browser $browser) {
            $browser->visit('/')
					->keys('#grant','50000')
					->assertDontSee('Invalid grant cost - must be greater than positive');
	});
	}
	
	public function testErrorInterest()
	{
		$this->browse(function (Browser $browser) {
            $browser->visit('/')
					->keys('#interest','-5')
					->assertSee('Invalid interest rate - must be between 0% and 100%')
					->keys('#interest','{backspace}','{backspace}')
					->keys('#interest','er')
					->assertSee('Invalid interest rate - must be between 0% and 100%');
	});
	}
	
	public function testGoodInterest()
	{
		$this->browse(function (Browser $browser) {
            $browser->visit('/')
					->keys('#interest','5')
					->assertDontSee('Invalid interest rate - must be between 0% and 100%');
	});
	}
	
	public function testErrorPowerCost()
	{
		$this->browse(function (Browser $browser) {
            $browser->visit('/')
					->keys('#powercost','-5')
					->assertSee('Invalid power cost - must be greater than 0');
	});
	}
	
	public function testGoodPowerCost()
	{
		$this->browse(function (Browser $browser) {
            $browser->visit('/')
					->keys('#powercost','0.15')
					->assertDontSee('Invalid power cost - must be greater than 0');
	});
	}
	
	public function testErrorLatitude2()
	{
		$this->browse(function (Browser $browser) {
            $browser->visit('/')
					->keys('#latitude','235')
					->assertSee('Invalid latitude - must be between -90 and 90')
					->keys('#latitude','{backspace}','{backspace}','{backspace}')
					->keys('#latitude','-95')
					->assertSee('Invalid latitude - must be between -90 and 90')
					->keys('#latitude','{backspace}','{backspace}','{backspace}')
					->keys('#latitude','er')
					->assertSee('Invalid latitude - must be between -90 and 90');
	});
	}
	
	public function testGoodLatitudeEdge()
	{
		$this->browse(function (Browser $browser) {
            $browser->visit('/')
					->keys('#latitude','90')
					->assertDontSee('Invalid latitude - must be between -90 and 90')
					->keys('#latitude','{backspace}','{backspace}')
					->keys('#latitude','0')
					->assertDontSee('Invalid latitude - must be between -90 and 90')
					->keys('#latitude','{backspace}')
					->keys('#latitude','-90')
					->assertDontSee('Invalid latitude - must be between -90 and 90');
	});
	}
	
	public function testErrorLongitudeEdge()
	{
		$this->browse(function (Browser $browser) {
            $browser->visit('/')
					->keys('#longitude','180')
					->assertDontSee('Invalid longitude - must be between -180 and 180')
					->keys('#longitude','{backspace}','{backspace}','{backspace}')
					->keys('#longitude','0')
					->assertDontSee('Invalid longitude - must be between -180 and 180')
					->keys('#longitude','{backspace}')
					->keys('#longitude','-180')
					->assertDontSee('Invalid longitude - must be between -180 and 180');
	});
	}
	
	public function testGoodGrantEdge()
	{
		$this->browse(function (Browser $browser) {
            $browser->visit('/')
					->keys('#grant','0')
					->assertDontSee('Invalid grant cost - must be greater than positive');
	});
	}
	
	public function testErrorInterestEdge()
	{
		$this->browse(function (Browser $browser) {
            $browser->visit('/')
					->keys('#interest','-1')
					->assertSee('Invalid interest rate - must be between 0% and 100%')
					->keys('#interest','{backspace}','{backspace}')
					->keys('#interest','101')
					->assertSee('Invalid interest rate - must be between 0% and 100%');
	});
	}
	
	public function testGoodInterestEdge()
	{
		$this->browse(function (Browser $browser) {
            $browser->visit('/')
					->keys('#interest','0')
					->assertDontSee('Invalid interest rate - must be between 0% and 100%')
					->keys('#interest','{backspace}')
					->keys('#interest','100')
					->assertDontSee('Invalid interest rate - must be between 0% and 100%');
	});
	}
	
	public function testErrorPowerCost2()
	{
		$this->browse(function (Browser $browser) {
            $browser->visit('/')
					->keys('#powercost','-5')
					->assertSee('Invalid power cost - must be greater than 0');
	});
	}
	
	public function testGoodPowerCost2()
	{
		$this->browse(function (Browser $browser) {
            $browser->visit('/')
					->keys('#powercost','0.15')
					->assertDontSee('Invalid power cost - must be greater than 0');
	});
	}
	
	
}

