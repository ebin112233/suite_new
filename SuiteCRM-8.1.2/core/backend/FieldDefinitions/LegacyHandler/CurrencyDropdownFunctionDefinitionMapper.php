<?php
/**
 * SuiteCRM is a customer relationship management program developed by SalesAgility Ltd.
 * Copyright (C) 2021 SalesAgility Ltd.
 *
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU Affero General Public License version 3 as published by the
 * Free Software Foundation with the addition of the following permission added
 * to Section 15 as permitted in Section 7(a): FOR ANY PART OF THE COVERED WORK
 * IN WHICH THE COPYRIGHT IS OWNED BY SALESAGILITY, SALESAGILITY DISCLAIMS THE
 * WARRANTY OF NON INFRINGEMENT OF THIRD PARTY RIGHTS.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more
 * details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * In accordance with Section 7(b) of the GNU Affero General Public License
 * version 3, these Appropriate Legal Notices must retain the display of the
 * "Supercharged by SuiteCRM" logo. If the display of the logos is not reasonably
 * feasible for technical reasons, the Appropriate Legal Notices must display
 * the words "Supercharged by SuiteCRM".
 */


namespace App\FieldDefinitions\LegacyHandler;

use App\Currency\LegacyHandler\CurrencyHandler;
use App\FieldDefinitions\Entity\FieldDefinition;

class CurrencyDropdownFunctionDefinitionMapper implements FieldDefinitionMapperInterface
{

    /**
     * @var CurrencyHandler
     */
    private $currencyHandler;

    /**
     * CurrencyDropdownFunctionDefinitionMapper constructor.
     * @param CurrencyHandler $currencyHandler
     */
    public function __construct(CurrencyHandler $currencyHandler)
    {
        $this->currencyHandler = $currencyHandler;
    }

    /**
     * @inheritDoc
     */

    public function getKey(): string
    {
        return 'currency-dropdown-field-definition-map';
    }

    /**
     * @inheritDoc
     */
    public function getModule(): string
    {
        return 'default';
    }

    /**
     * @inheritDoc
     * @param FieldDefinition $definition
     */
    public function map(FieldDefinition $definition): void
    {
        $vardefs = $definition->getVardef();

        $currencies = $this->currencyHandler->getCurrencies();


        if (empty($currencies)) {
            return;
        }

        foreach ($vardefs as $fieldName => $fieldDefinition) {

            $function = $fieldDefinition['function'] ?? '';

            if (empty($function)) {
                continue;
            }

            if (is_array($fieldDefinition['function'])) {
                $function = $fieldDefinition['function']['name'] ?? '';
            }

            if ($function !== 'getCurrencyDropDown') {
                continue;
            }

            $metadata = $fieldDefinition['metadata'] ?? [];
            $metadata['extraOptions'] = [];

            foreach ($currencies as $currency) {

                $metadata['extraOptions'][] = [
                    'value' => $currency['id'],
                    'label' => $currency['iso4217']
                ];
            }

            $fieldDefinition['metadata'] = $metadata;

            $vardefs[$fieldName] = $fieldDefinition;
        }

        $definition->setVardef($vardefs);
    }
}
