<?php

/**
 * InfusionCrafting\Exception\ApiException class
 *
 * @copyright 2018 SiteCrafting, Inc.
 * @author    Coby Tamayo <ctamayo@sitecrafting.com>
 */
namespace InfusionCrafting\Exception;

use Infusionsoft\Http\HttpException;

/**
 * Class to wrap exceptions thrown by the InfusionSoft API SDK
 */
class ApiException extends HttpException {}
