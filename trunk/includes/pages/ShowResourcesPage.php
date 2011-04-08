<?php

/**
 *  2Moons
 *  Copyright (C) 2011  Slaver
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package 2Moons
 * @author Slaver <slaver7@gmail.com>
 * @copyright 2009 Lucky <lucky@xgproyect.net> (XGProyecto)
 * @copyright 2011 Slaver <slaver7@gmail.com> (Fork/2Moons)
 * @license http://www.gnu.org/licenses/gpl.html GNU GPLv3 License
 * @version 1.3 (2011-01-21)
 * @link http://code.google.com/p/2moons/
 */

function ShowResourcesPage()
{
	global $LNG, $ProdGrid, $resource, $reslist, $CONF, $db, $ExtraDM, $USER, $PLANET, $OfficerInfo;
	
	if ($PLANET['planet_type'] == 3 || $USER['urlaubs_modus'] == 1)
	{
		$CONF['metal_basic_income']     = 0;
		$CONF['crystal_basic_income']   = 0;
		$CONF['deuterium_basic_income'] = 0;
	}

	$SubQry               = "";
	if (!empty($_POST) && $USER['urlaubs_modus'] == 0)
	{
		foreach($_POST as $Field => $Value)
		{
			$FieldName = $Field."_porcent";
			if (isset($PLANET[$FieldName]) && in_array($Value, $reslist['procent']))
			{
				$Value				= $Value / 10;
				$PLANET[$FieldName]	= $Value;
				$SubQry				.= ", `".$FieldName."` = '".$Value."'";
			}
		}
		if(isset($SubQry))
		{
			$SQL  = "UPDATE ".PLANETS." SET ";
			$SQL .= "`id` = '". $PLANET['id'] ."' ";
			$SQL .= $SubQry;
			$SQL .= "WHERE ";
			$SQL .= "`id` = '". $PLANET['id'] ."';";
			$db->query($SQL);
			redirectTo("game.php?page=resources");
		}
		exit;		
	}
	
	$PlanetRess = new ResourceUpdate();
	$PlanetRess->CalcResource();
	$PlanetRess->SavePlanetToDB();

	$template	= new template();
	
	if ($PLANET['energy_max'] == 0 && $PLANET['energy_used'] > 0)
	{
		$post_porcent = 0;
	}
	elseif ($PLANET['energy_max']  > 0 && abs($PLANET['energy_used']) > $PLANET['energy_max'])
	{
		$post_porcent = floor(($PLANET['energy_max']) / ($PLANET['energy_used']*-1) * 100);
	}
	elseif ($PLANET['energy_max'] == 0 && abs($PLANET['energy_used']) > $PLANET['energy_max'])
	{
		$post_porcent = 0;
	}
	else
	{
		$post_porcent = 100;
	}

	if ($post_porcent > 100)
	{
		$post_porcent = 100;
	}
	
	$BuildTemp      = $PLANET['temp_max'];
	$BuildEnergy	= $USER[$resource[113]];
	$metal			= array();
	$crystal		= array();
	$deuterium		= array();
	$deu_en			= array();
	$energy			= array();
	$energy_en		= array();
	
	foreach($reslist['prod'] as $ProdID)
	{
		if ($PLANET[$resource[$ProdID]] > 0 && isset($ProdGrid[$ProdID]))
		{
			$BuildLevelFactor       = $PLANET[$resource[$ProdID]."_porcent"];
			$BuildLevel             = $PLANET[$resource[$ProdID]];
			$metal[$ProdID]     	= floor(eval($ProdGrid[$ProdID]['formule']['metal'])     * (0.01 * $post_porcent) * $CONF['resource_multiplier']);
			$crystal[$ProdID] 		= floor(eval($ProdGrid[$ProdID]['formule']['crystal'])   * (0.01 * $post_porcent) * $CONF['resource_multiplier']);
		
			if ($ProdID < 4)
			{
				$deuterium[$ProdID]	= floor(eval($ProdGrid[$ProdID]['formule']['deuterium']) * (0.01 * $post_porcent) * $CONF['resource_multiplier']);
				$energy[$ProdID]	= floor(eval($ProdGrid[$ProdID]['formule']['energy'])    * $CONF['resource_multiplier']);
			} else {
				if($ProdID == 12 && $PLANET['deuterium'] == 0)
					continue;
				
				$deu_en[$ProdID]	= floor(eval($ProdGrid[$ProdID]['formule']['deuterium']) * $CONF['resource_multiplier']);
				$energy_en[$ProdID]	= floor(eval($ProdGrid[$ProdID]['formule']['energy']) * $CONF['resource_multiplier']);
			}

			$thisdeu				= ((isset($deuterium[$ProdID])) ? $deuterium[$ProdID] : $deu_en[$ProdID]);
			$thisenergy				= ((isset($energy[$ProdID])) ? $energy[$ProdID] : $energy_en[$ProdID]);

			$CurrPlanetList[]	= array(
				'name'              => $resource[$ProdID],
				'type'  			=> $LNG['tech'][$ProdID],
				'level'     	    => ($ProdID > 200) ? $LNG['rs_amount'] : $LNG['rs_lvl'],
				'level_type'        => $PLANET[$resource[$ProdID]],
				'metal_type'        => colorNumber(pretty_number($metal[$ProdID])),
				'crystal_type'      => colorNumber(pretty_number($crystal[$ProdID])),
				'deuterium_type'    => colorNumber(pretty_number($thisdeu)),
				'energy_type'       => colorNumber(pretty_number($thisenergy)),
				'optionsel'			=> $PLANET[$resource[$ProdID]."_porcent"] * 10,
			);
		}
	}


	$metal_total		            = floor($PLANET['metal_perhour'] + $CONF['metal_basic_income'] * $CONF['resource_multiplier']);
	$crystal_total			        = floor($PLANET['crystal_perhour'] + $CONF['crystal_basic_income'] * $CONF['resource_multiplier']);
	$deuterium_total  		        = floor($PLANET['deuterium_perhour'] + $CONF['deuterium_basic_income'] * $CONF['resource_multiplier']);
	$energy_total					= floor($PLANET['energy_max'] + $CONF['energy_basic_income'] * $CONF['resource_multiplier'] - abs($PLANET['energy_used']));
	
	foreach($reslist['procent'] as $procent){
		$OptionSelector[$procent]	= $procent."%";
	}

	$template->assign_vars(array(	
		'bonus_metal'							=> colorNumber(pretty_number($PLANET['metal_perhour'] - array_sum($metal))),
		'bonus_crystal'							=> colorNumber(pretty_number($PLANET['crystal_perhour'] - array_sum($crystal))),
		'bonus_deuterium'						=> colorNumber(pretty_number($PLANET['deuterium_perhour'] - array_sum($deuterium))),
		'bonus_energy'							=> colorNumber(pretty_number($PLANET['energy_max'] - array_sum($energy_en))),
		'CurrPlanetList'						=> $CurrPlanetList,	
		'Production_of_resources_in_the_planet'	=> str_replace('%s', $PLANET['name'], $LNG['rs_production_on_planet']),
		'metal_basic_income'    				=> $CONF['metal_basic_income']     * $CONF['resource_multiplier'],
		'crystal_basic_income'  				=> $CONF['crystal_basic_income']   * $CONF['resource_multiplier'],
		'deuterium_basic_income'				=> $CONF['deuterium_basic_income'] * $CONF['resource_multiplier'],
		'energy_basic_income'   				=> $CONF['energy_basic_income']    * $CONF['resource_multiplier'],
		'metalmax'             					=> colorNumber($PLANET['metal_max'] / 1000, pretty_number($PLANET['metal_max'] / 1000) ."k"),
		'crystalmax'          					=> colorNumber($PLANET['crystal_max'] / 1000, pretty_number($PLANET['crystal_max'] / 1000) ."k"),
		'deuteriummax'         					=> colorNumber($PLANET['deuterium_max'] / 1000, pretty_number($PLANET['deuterium_max'] / 1000) ."k"),
		'metal_total'           				=> colorNumber(pretty_number($metal_total)),
		'crystal_total'         				=> colorNumber(pretty_number($crystal_total)),
		'option'								=> $OptionSelector,
		'deuterium_total'       				=> colorNumber(pretty_number($deuterium_total)),
		'energy_total'          				=> colorNumber(pretty_number($energy_total)),
		'daily_metal'           				=> colorNumber(pretty_number(floor($metal_total     * 24))),
		'weekly_metal'          				=> colorNumber(pretty_number(floor($metal_total     * 24 * 7))),
		'daily_crystal'         				=> colorNumber(pretty_number(floor($crystal_total   * 24))),
		'weekly_crystal'        				=> colorNumber(pretty_number(floor($crystal_total   * 24 * 7))),
		'daily_deuterium'       				=> colorNumber(pretty_number(floor($deuterium_total * 24))),
		'weekly_deuterium'      				=> colorNumber(pretty_number(floor($deuterium_total * 24 * 7))),
		'Metal'									=> $LNG['Metal'], 
		'Crystal'								=> $LNG['Crystal'], 
		'Deuterium'								=> $LNG['Deuterium'], 
		'Energy'								=> $LNG['Energy'],
		'rs_basic_income'						=> $LNG['rs_basic_income'], 
		'rs_storage_capacity'					=> $LNG['rs_storage_capacity'], 
		'rs_sum'								=> $LNG['rs_sum'], 
		'rs_daily'								=> $LNG['rs_daily'], 
		'rs_weekly'								=> $LNG['rs_weekly'], 	
		'rs_calculate'							=> $LNG['rs_calculate'], 	
		'rs_ress_bonus'							=> $LNG['rs_ress_bonus'], 	
	));
	
	$template->show("resources_overview.tpl");
}

?>