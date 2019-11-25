<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *                                   ATTENTION!
 * If you see this message in your browser (Internet Explorer, Mozilla Firefox, Google Chrome, etc.)
 * this means that PHP is not properly installed on your web server. Please refer to the PHP manual
 * for more details: http://php.net/manual/install.php 
 *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 */

    include_once dirname(__FILE__) . '/components/startup.php';
    include_once dirname(__FILE__) . '/components/application.php';
    include_once dirname(__FILE__) . '/' . 'authorization.php';


    include_once dirname(__FILE__) . '/' . 'database_engine/mysql_engine.php';
    include_once dirname(__FILE__) . '/' . 'components/page/page_includes.php';

    function GetConnectionOptions()
    {
        $result = GetGlobalConnectionOptions();
        $result['client_encoding'] = 'utf8';
        GetApplication()->GetUserAuthentication()->applyIdentityToConnectionOptions($result);
        return $result;
    }

    
    
    
    // OnBeforePageExecute event handler
    
    
    
    class funcionárioPage extends Page
    {
        protected function DoBeforeCreate()
        {
            $this->dataset = new TableDataset(
                MyPDOConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`funcionário`');
            $this->dataset->addFields(
                array(
                    new IntegerField('ID', true, true, true),
                    new StringField('Nome do funcionario(a)', true),
                    new IntegerField('CPF', true),
                    new IntegerField('RG', true),
                    new DateField('Data de contratação', true),
                    new StringField('Cargo', true),
                    new StringField('Registro da Profissão', true),
                    new IntegerField('Contato', true),
                    new StringField('E-mail', true),
                    new IntegerField('Salário', true),
                    new DateField('Data de Pagamento', true)
                )
            );
        }
    
        protected function DoPrepare() {
    
        }
    
        protected function CreatePageNavigator()
        {
            $result = new CompositePageNavigator($this);
            
            $partitionNavigator = new PageNavigator('pnav', $this, $this->dataset);
            $partitionNavigator->SetRowsPerPage(20);
            $result->AddPageNavigator($partitionNavigator);
            
            return $result;
        }
    
        protected function CreateRssGenerator()
        {
            return null;
        }
    
        protected function setupCharts()
        {
    
        }
    
        protected function getFiltersColumns()
        {
            return array(
                new FilterColumn($this->dataset, 'ID', 'ID', 'ID'),
                new FilterColumn($this->dataset, 'Nome do funcionario(a)', 'Nome do funcionario(a)', 'Nome Do Funcionario(a)'),
                new FilterColumn($this->dataset, 'CPF', 'CPF', 'CPF'),
                new FilterColumn($this->dataset, 'RG', 'RG', 'RG'),
                new FilterColumn($this->dataset, 'Data de contratação', 'Data de contratação', 'Data De Contratação'),
                new FilterColumn($this->dataset, 'Cargo', 'Cargo', 'Cargo'),
                new FilterColumn($this->dataset, 'Registro da Profissão', 'Registro da Profissão', 'Registro Da Profissão'),
                new FilterColumn($this->dataset, 'Contato', 'Contato', 'Contato'),
                new FilterColumn($this->dataset, 'E-mail', 'E-mail', 'E-mail'),
                new FilterColumn($this->dataset, 'Salário', 'Salário', 'Salário'),
                new FilterColumn($this->dataset, 'Data de Pagamento', 'Data de Pagamento', 'Data De Pagamento')
            );
        }
    
        protected function setupQuickFilter(QuickFilter $quickFilter, FixedKeysArray $columns)
        {
            $quickFilter
                ->addColumn($columns['Nome do funcionario(a)'])
                ->addColumn($columns['Cargo']);
        }
    
        protected function setupColumnFilter(ColumnFilter $columnFilter)
        {
    
        }
    
        protected function setupFilterBuilder(FilterBuilder $filterBuilder, FixedKeysArray $columns)
        {
            $main_editor = new TextEdit('nome_do_funcionario(a)_edit');
            $main_editor->SetMaxLength(100);
            
            $filterBuilder->addColumn(
                $columns['Nome do funcionario(a)'],
                array(
                    FilterConditionOperator::EQUALS => $main_editor,
                    FilterConditionOperator::DOES_NOT_EQUAL => $main_editor
                )
            );
            
            $main_editor = new ComboBox('Cargo');
            $main_editor->SetAllowNullValue(false);
            $main_editor->addChoice('Veterinario(a)', 'Veterinario(a)');
            $main_editor->addChoice('Tecnico(a)', 'Tecnico(a)');
            $main_editor->addChoice('Serviços gerais', 'Serviços gerais');
            
            $multi_value_select_editor = new MultiValueSelect('Cargo');
            $multi_value_select_editor->setChoices($main_editor->getChoices());
            
            $filterBuilder->addColumn(
                $columns['Cargo'],
                array(
                    FilterConditionOperator::EQUALS => $main_editor,
                    FilterConditionOperator::IN => $multi_value_select_editor,
                    FilterConditionOperator::NOT_IN => $multi_value_select_editor
                )
            );
        }
    
        protected function AddOperationsColumns(Grid $grid)
        {
            $actions = $grid->getActions();
            $actions->setCaption($this->GetLocalizerCaptions()->GetMessageString('Actions'));
            $actions->setPosition(ActionList::POSITION_LEFT);
            
            if ($this->GetSecurityInfo()->HasViewGrant())
            {
                $operation = new LinkOperation($this->GetLocalizerCaptions()->GetMessageString('View'), OPERATION_VIEW, $this->dataset, $grid);
                $operation->setUseImage(true);
                $actions->addOperation($operation);
            }
            
            if ($this->GetSecurityInfo()->HasEditGrant())
            {
                $operation = new LinkOperation($this->GetLocalizerCaptions()->GetMessageString('Edit'), OPERATION_EDIT, $this->dataset, $grid);
                $operation->setUseImage(true);
                $actions->addOperation($operation);
                $operation->OnShow->AddListener('ShowEditButtonHandler', $this);
            }
            
            if ($this->GetSecurityInfo()->HasDeleteGrant())
            {
                $operation = new LinkOperation($this->GetLocalizerCaptions()->GetMessageString('Delete'), OPERATION_DELETE, $this->dataset, $grid);
                $operation->setUseImage(true);
                $actions->addOperation($operation);
                $operation->OnShow->AddListener('ShowDeleteButtonHandler', $this);
                $operation->SetAdditionalAttribute('data-modal-operation', 'delete');
                $operation->SetAdditionalAttribute('data-delete-handler-name', $this->GetModalGridDeleteHandler());
            }
            
            if ($this->GetSecurityInfo()->HasAddGrant())
            {
                $operation = new LinkOperation($this->GetLocalizerCaptions()->GetMessageString('Copy'), OPERATION_COPY, $this->dataset, $grid);
                $operation->setUseImage(true);
                $actions->addOperation($operation);
            }
        }
    
        protected function AddFieldColumns(Grid $grid, $withDetails = true)
        {
            //
            // View column for Nome do funcionario(a) field
            //
            $column = new TextViewColumn('Nome do funcionario(a)', 'Nome do funcionario(a)', 'Nome Do Funcionario(a)', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('funcionárioGrid_Nome do funcionario(a)_handler_list');
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->SetDescription('');
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for Cargo field
            //
            $column = new TextViewColumn('Cargo', 'Cargo', 'Cargo', $this->dataset);
            $column->SetOrderable(true);
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->SetDescription('');
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for E-mail field
            //
            $column = new TextViewColumn('E-mail', 'E-mail', 'E-mail', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('funcionárioGrid_E-mail_handler_list');
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->SetDescription('');
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for Salário field
            //
            $column = new NumberViewColumn('Salário', 'Salário', 'Salário', $this->dataset);
            $column->SetOrderable(true);
            $column->setNumberAfterDecimal(0);
            $column->setThousandsSeparator(',');
            $column->setDecimalSeparator('');
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->SetDescription('');
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
        }
    
        protected function AddSingleRecordViewColumns(Grid $grid)
        {
            //
            // View column for Nome do funcionario(a) field
            //
            $column = new TextViewColumn('Nome do funcionario(a)', 'Nome do funcionario(a)', 'Nome Do Funcionario(a)', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('funcionárioGrid_Nome do funcionario(a)_handler_view');
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for CPF field
            //
            $column = new NumberViewColumn('CPF', 'CPF', 'CPF', $this->dataset);
            $column->SetOrderable(true);
            $column->setNumberAfterDecimal(0);
            $column->setThousandsSeparator(',');
            $column->setDecimalSeparator('');
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for RG field
            //
            $column = new NumberViewColumn('RG', 'RG', 'RG', $this->dataset);
            $column->SetOrderable(true);
            $column->setNumberAfterDecimal(0);
            $column->setThousandsSeparator(',');
            $column->setDecimalSeparator('');
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for Data de contratação field
            //
            $column = new DateTimeViewColumn('Data de contratação', 'Data de contratação', 'Data De Contratação', $this->dataset);
            $column->SetOrderable(true);
            $column->SetDateTimeFormat('Y-m-d');
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for Cargo field
            //
            $column = new TextViewColumn('Cargo', 'Cargo', 'Cargo', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for Registro da Profissão field
            //
            $column = new TextViewColumn('Registro da Profissão', 'Registro da Profissão', 'Registro Da Profissão', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('funcionárioGrid_Registro da Profissão_handler_view');
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for Contato field
            //
            $column = new CheckboxViewColumn('Contato', 'Contato', 'Contato', $this->dataset);
            $column->SetOrderable(true);
            $column->setDisplayValues('<span class="pg-row-checkbox checked"></span>', '<span class="pg-row-checkbox"></span>');
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for E-mail field
            //
            $column = new TextViewColumn('E-mail', 'E-mail', 'E-mail', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('funcionárioGrid_E-mail_handler_view');
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for Salário field
            //
            $column = new NumberViewColumn('Salário', 'Salário', 'Salário', $this->dataset);
            $column->SetOrderable(true);
            $column->setNumberAfterDecimal(0);
            $column->setThousandsSeparator(',');
            $column->setDecimalSeparator('');
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for Data de Pagamento field
            //
            $column = new DateTimeViewColumn('Data de Pagamento', 'Data de Pagamento', 'Data De Pagamento', $this->dataset);
            $column->SetOrderable(true);
            $column->SetDateTimeFormat('Y-m-d');
            $grid->AddSingleRecordViewColumn($column);
        }
    
        protected function AddEditColumns(Grid $grid)
        {
            //
            // Edit column for Cargo field
            //
            $editor = new RadioEdit('cargo_edit');
            $editor->SetDisplayMode(RadioEdit::StackedMode);
            $editor->addChoice('Veterinario(a)', 'Veterinario(a)');
            $editor->addChoice('Tecnico(a)', 'Tecnico(a)');
            $editor->addChoice('Serviços gerais', 'Serviços gerais');
            $editColumn = new CustomEditColumn('Cargo', 'Cargo', $editor, $this->dataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for Contato field
            //
            $editor = new TextEdit('contato_edit');
            $editColumn = new CustomEditColumn('Contato', 'Contato', $editor, $this->dataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for E-mail field
            //
            $editor = new TextEdit('e-mail_edit');
            $editor->SetMaxLength(100);
            $editColumn = new CustomEditColumn('E-mail', 'E-mail', $editor, $this->dataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for Salário field
            //
            $editor = new TextEdit('salário_edit');
            $editColumn = new CustomEditColumn('Salário', 'Salário', $editor, $this->dataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for Data de Pagamento field
            //
            $editor = new DateTimeEdit('data_de_pagamento_edit', false, 'Y-m-d H:i:s');
            $editColumn = new CustomEditColumn('Data De Pagamento', 'Data de Pagamento', $editor, $this->dataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
        }
    
        protected function AddMultiEditColumns(Grid $grid)
        {
            //
            // Edit column for Cargo field
            //
            $editor = new RadioEdit('cargo_edit');
            $editor->SetDisplayMode(RadioEdit::StackedMode);
            $editor->addChoice('Veterinario(a)', 'Veterinario(a)');
            $editor->addChoice('Tecnico(a)', 'Tecnico(a)');
            $editor->addChoice('Serviços gerais', 'Serviços gerais');
            $editColumn = new CustomEditColumn('Cargo', 'Cargo', $editor, $this->dataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddMultiEditColumn($editColumn);
            
            //
            // Edit column for Contato field
            //
            $editor = new TextEdit('contato_edit');
            $editColumn = new CustomEditColumn('Contato', 'Contato', $editor, $this->dataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddMultiEditColumn($editColumn);
            
            //
            // Edit column for E-mail field
            //
            $editor = new TextEdit('e-mail_edit');
            $editor->SetMaxLength(100);
            $editColumn = new CustomEditColumn('E-mail', 'E-mail', $editor, $this->dataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddMultiEditColumn($editColumn);
            
            //
            // Edit column for Salário field
            //
            $editor = new TextEdit('salário_edit');
            $editColumn = new CustomEditColumn('Salário', 'Salário', $editor, $this->dataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddMultiEditColumn($editColumn);
            
            //
            // Edit column for Data de Pagamento field
            //
            $editor = new DateTimeEdit('data_de_pagamento_edit', false, 'Y-m-d H:i:s');
            $editColumn = new CustomEditColumn('Data De Pagamento', 'Data de Pagamento', $editor, $this->dataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddMultiEditColumn($editColumn);
        }
    
        protected function AddInsertColumns(Grid $grid)
        {
            //
            // Edit column for Nome do funcionario(a) field
            //
            $editor = new TextEdit('nome_do_funcionario(a)_edit');
            $editor->SetMaxLength(100);
            $editColumn = new CustomEditColumn('Nome Do Funcionario(a)', 'Nome do funcionario(a)', $editor, $this->dataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for CPF field
            //
            $editor = new TextEdit('cpf_edit');
            $editColumn = new CustomEditColumn('CPF', 'CPF', $editor, $this->dataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $validator = new MaxLengthValidator(11, StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('MaxlengthValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $validator = new MinLengthValidator(11, StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('MinlengthValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for RG field
            //
            $editor = new TextEdit('rg_edit');
            $editColumn = new CustomEditColumn('RG', 'RG', $editor, $this->dataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $validator = new MaxLengthValidator(9, StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('MaxlengthValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $validator = new MinLengthValidator(9, StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('MinlengthValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for Data de contratação field
            //
            $editor = new DateTimeEdit('data_de_contratação_edit', false, 'Y-m-d');
            $editColumn = new CustomEditColumn('Data De Contratação', 'Data de contratação', $editor, $this->dataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for Cargo field
            //
            $editor = new RadioEdit('cargo_edit');
            $editor->SetDisplayMode(RadioEdit::StackedMode);
            $editor->addChoice('Veterinario(a)', 'Veterinario(a)');
            $editor->addChoice('Tecnico(a)', 'Tecnico(a)');
            $editor->addChoice('Serviços gerais', 'Serviços gerais');
            $editColumn = new CustomEditColumn('Cargo', 'Cargo', $editor, $this->dataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for Registro da Profissão field
            //
            $editor = new TextAreaEdit('registro_da_profissão_edit', 50, 8);
            $editColumn = new CustomEditColumn('Registro Da Profissão', 'Registro da Profissão', $editor, $this->dataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for Contato field
            //
            $editor = new TextEdit('contato_edit');
            $editColumn = new CustomEditColumn('Contato', 'Contato', $editor, $this->dataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for E-mail field
            //
            $editor = new TextEdit('e-mail_edit');
            $editor->SetMaxLength(100);
            $editColumn = new CustomEditColumn('E-mail', 'E-mail', $editor, $this->dataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for Salário field
            //
            $editor = new TextEdit('salário_edit');
            $editColumn = new CustomEditColumn('Salário', 'Salário', $editor, $this->dataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for Data de Pagamento field
            //
            $editor = new DateTimeEdit('data_de_pagamento_edit', false, 'Y-m-d H:i:s');
            $editColumn = new CustomEditColumn('Data De Pagamento', 'Data de Pagamento', $editor, $this->dataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            $grid->SetShowAddButton(true && $this->GetSecurityInfo()->HasAddGrant());
        }
    
        private function AddMultiUploadColumn(Grid $grid)
        {
    
        }
    
        protected function AddPrintColumns(Grid $grid)
        {
            //
            // View column for ID field
            //
            $column = new NumberViewColumn('ID', 'ID', 'ID', $this->dataset);
            $column->SetOrderable(true);
            $column->setNumberAfterDecimal(0);
            $column->setThousandsSeparator(',');
            $column->setDecimalSeparator('');
            $grid->AddPrintColumn($column);
            
            //
            // View column for Nome do funcionario(a) field
            //
            $column = new TextViewColumn('Nome do funcionario(a)', 'Nome do funcionario(a)', 'Nome Do Funcionario(a)', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('funcionárioGrid_Nome do funcionario(a)_handler_print');
            $grid->AddPrintColumn($column);
            
            //
            // View column for CPF field
            //
            $column = new NumberViewColumn('CPF', 'CPF', 'CPF', $this->dataset);
            $column->SetOrderable(true);
            $column->setNumberAfterDecimal(0);
            $column->setThousandsSeparator(',');
            $column->setDecimalSeparator('');
            $grid->AddPrintColumn($column);
            
            //
            // View column for RG field
            //
            $column = new NumberViewColumn('RG', 'RG', 'RG', $this->dataset);
            $column->SetOrderable(true);
            $column->setNumberAfterDecimal(0);
            $column->setThousandsSeparator(',');
            $column->setDecimalSeparator('');
            $grid->AddPrintColumn($column);
            
            //
            // View column for Data de contratação field
            //
            $column = new DateTimeViewColumn('Data de contratação', 'Data de contratação', 'Data De Contratação', $this->dataset);
            $column->SetOrderable(true);
            $column->SetDateTimeFormat('Y-m-d');
            $grid->AddPrintColumn($column);
            
            //
            // View column for Cargo field
            //
            $column = new TextViewColumn('Cargo', 'Cargo', 'Cargo', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for Registro da Profissão field
            //
            $column = new TextViewColumn('Registro da Profissão', 'Registro da Profissão', 'Registro Da Profissão', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('funcionárioGrid_Registro da Profissão_handler_print');
            $grid->AddPrintColumn($column);
            
            //
            // View column for Contato field
            //
            $column = new CheckboxViewColumn('Contato', 'Contato', 'Contato', $this->dataset);
            $column->SetOrderable(true);
            $column->setDisplayValues('<span class="pg-row-checkbox checked"></span>', '<span class="pg-row-checkbox"></span>');
            $grid->AddPrintColumn($column);
            
            //
            // View column for E-mail field
            //
            $column = new TextViewColumn('E-mail', 'E-mail', 'E-mail', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('funcionárioGrid_E-mail_handler_print');
            $grid->AddPrintColumn($column);
            
            //
            // View column for Salário field
            //
            $column = new NumberViewColumn('Salário', 'Salário', 'Salário', $this->dataset);
            $column->SetOrderable(true);
            $column->setNumberAfterDecimal(0);
            $column->setThousandsSeparator(',');
            $column->setDecimalSeparator('');
            $grid->AddPrintColumn($column);
            
            //
            // View column for Data de Pagamento field
            //
            $column = new DateTimeViewColumn('Data de Pagamento', 'Data de Pagamento', 'Data De Pagamento', $this->dataset);
            $column->SetOrderable(true);
            $column->SetDateTimeFormat('Y-m-d');
            $grid->AddPrintColumn($column);
        }
    
        protected function AddExportColumns(Grid $grid)
        {
            //
            // View column for ID field
            //
            $column = new NumberViewColumn('ID', 'ID', 'ID', $this->dataset);
            $column->SetOrderable(true);
            $column->setNumberAfterDecimal(0);
            $column->setThousandsSeparator(',');
            $column->setDecimalSeparator('');
            $grid->AddExportColumn($column);
            
            //
            // View column for Nome do funcionario(a) field
            //
            $column = new TextViewColumn('Nome do funcionario(a)', 'Nome do funcionario(a)', 'Nome Do Funcionario(a)', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('funcionárioGrid_Nome do funcionario(a)_handler_export');
            $grid->AddExportColumn($column);
            
            //
            // View column for CPF field
            //
            $column = new NumberViewColumn('CPF', 'CPF', 'CPF', $this->dataset);
            $column->SetOrderable(true);
            $column->setNumberAfterDecimal(0);
            $column->setThousandsSeparator(',');
            $column->setDecimalSeparator('');
            $grid->AddExportColumn($column);
            
            //
            // View column for RG field
            //
            $column = new NumberViewColumn('RG', 'RG', 'RG', $this->dataset);
            $column->SetOrderable(true);
            $column->setNumberAfterDecimal(0);
            $column->setThousandsSeparator(',');
            $column->setDecimalSeparator('');
            $grid->AddExportColumn($column);
            
            //
            // View column for Data de contratação field
            //
            $column = new DateTimeViewColumn('Data de contratação', 'Data de contratação', 'Data De Contratação', $this->dataset);
            $column->SetOrderable(true);
            $column->SetDateTimeFormat('Y-m-d');
            $grid->AddExportColumn($column);
            
            //
            // View column for Cargo field
            //
            $column = new TextViewColumn('Cargo', 'Cargo', 'Cargo', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for Registro da Profissão field
            //
            $column = new TextViewColumn('Registro da Profissão', 'Registro da Profissão', 'Registro Da Profissão', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('funcionárioGrid_Registro da Profissão_handler_export');
            $grid->AddExportColumn($column);
            
            //
            // View column for Contato field
            //
            $column = new CheckboxViewColumn('Contato', 'Contato', 'Contato', $this->dataset);
            $column->SetOrderable(true);
            $column->setDisplayValues('<span class="pg-row-checkbox checked"></span>', '<span class="pg-row-checkbox"></span>');
            $grid->AddExportColumn($column);
            
            //
            // View column for E-mail field
            //
            $column = new TextViewColumn('E-mail', 'E-mail', 'E-mail', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('funcionárioGrid_E-mail_handler_export');
            $grid->AddExportColumn($column);
            
            //
            // View column for Salário field
            //
            $column = new NumberViewColumn('Salário', 'Salário', 'Salário', $this->dataset);
            $column->SetOrderable(true);
            $column->setNumberAfterDecimal(0);
            $column->setThousandsSeparator(',');
            $column->setDecimalSeparator('');
            $grid->AddExportColumn($column);
            
            //
            // View column for Data de Pagamento field
            //
            $column = new DateTimeViewColumn('Data de Pagamento', 'Data de Pagamento', 'Data De Pagamento', $this->dataset);
            $column->SetOrderable(true);
            $column->SetDateTimeFormat('Y-m-d');
            $grid->AddExportColumn($column);
        }
    
        private function AddCompareColumns(Grid $grid)
        {
            //
            // View column for Nome do funcionario(a) field
            //
            $column = new TextViewColumn('Nome do funcionario(a)', 'Nome do funcionario(a)', 'Nome Do Funcionario(a)', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('funcionárioGrid_Nome do funcionario(a)_handler_compare');
            $grid->AddCompareColumn($column);
            
            //
            // View column for CPF field
            //
            $column = new NumberViewColumn('CPF', 'CPF', 'CPF', $this->dataset);
            $column->SetOrderable(true);
            $column->setNumberAfterDecimal(0);
            $column->setThousandsSeparator(',');
            $column->setDecimalSeparator('');
            $grid->AddCompareColumn($column);
            
            //
            // View column for RG field
            //
            $column = new NumberViewColumn('RG', 'RG', 'RG', $this->dataset);
            $column->SetOrderable(true);
            $column->setNumberAfterDecimal(0);
            $column->setThousandsSeparator(',');
            $column->setDecimalSeparator('');
            $grid->AddCompareColumn($column);
            
            //
            // View column for Data de contratação field
            //
            $column = new DateTimeViewColumn('Data de contratação', 'Data de contratação', 'Data De Contratação', $this->dataset);
            $column->SetOrderable(true);
            $column->SetDateTimeFormat('Y-m-d');
            $grid->AddCompareColumn($column);
            
            //
            // View column for Cargo field
            //
            $column = new TextViewColumn('Cargo', 'Cargo', 'Cargo', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddCompareColumn($column);
            
            //
            // View column for Registro da Profissão field
            //
            $column = new TextViewColumn('Registro da Profissão', 'Registro da Profissão', 'Registro Da Profissão', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('funcionárioGrid_Registro da Profissão_handler_compare');
            $grid->AddCompareColumn($column);
            
            //
            // View column for Contato field
            //
            $column = new CheckboxViewColumn('Contato', 'Contato', 'Contato', $this->dataset);
            $column->SetOrderable(true);
            $column->setDisplayValues('<span class="pg-row-checkbox checked"></span>', '<span class="pg-row-checkbox"></span>');
            $grid->AddCompareColumn($column);
            
            //
            // View column for E-mail field
            //
            $column = new TextViewColumn('E-mail', 'E-mail', 'E-mail', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('funcionárioGrid_E-mail_handler_compare');
            $grid->AddCompareColumn($column);
            
            //
            // View column for Salário field
            //
            $column = new NumberViewColumn('Salário', 'Salário', 'Salário', $this->dataset);
            $column->SetOrderable(true);
            $column->setNumberAfterDecimal(0);
            $column->setThousandsSeparator(',');
            $column->setDecimalSeparator('');
            $grid->AddCompareColumn($column);
            
            //
            // View column for Data de Pagamento field
            //
            $column = new DateTimeViewColumn('Data de Pagamento', 'Data de Pagamento', 'Data De Pagamento', $this->dataset);
            $column->SetOrderable(true);
            $column->SetDateTimeFormat('Y-m-d');
            $grid->AddCompareColumn($column);
        }
    
        private function AddCompareHeaderColumns(Grid $grid)
        {
    
        }
    
        public function GetPageDirection()
        {
            return null;
        }
    
        public function isFilterConditionRequired()
        {
            return false;
        }
    
        protected function ApplyCommonColumnEditProperties(CustomEditColumn $column)
        {
            $column->SetDisplaySetToNullCheckBox(false);
            $column->SetDisplaySetToDefaultCheckBox(false);
    		$column->SetVariableContainer($this->GetColumnVariableContainer());
        }
    
        function GetCustomClientScript()
        {
            return ;
        }
        
        function GetOnPageLoadedClientScript()
        {
            return ;
        }
        protected function GetEnableModalGridDelete() { return true; }
    
        protected function CreateGrid()
        {
            $result = new Grid($this, $this->dataset);
            if ($this->GetSecurityInfo()->HasDeleteGrant())
               $result->SetAllowDeleteSelected(true);
            else
               $result->SetAllowDeleteSelected(false);   
            
            ApplyCommonPageSettings($this, $result);
            
            $result->SetUseImagesForActions(true);
            $result->SetUseFixedHeader(false);
            $result->SetShowLineNumbers(false);
            $result->SetShowKeyColumnsImagesInHeader(false);
            $result->SetViewMode(ViewMode::TABLE);
            $result->setEnableRuntimeCustomization(true);
            $result->setAllowCompare(true);
            $this->AddCompareHeaderColumns($result);
            $this->AddCompareColumns($result);
            $result->setMultiEditAllowed($this->GetSecurityInfo()->HasEditGrant() && true);
            $result->setTableBordered(false);
            $result->setTableCondensed(false);
            
            $result->SetHighlightRowAtHover(false);
            $result->SetWidth('');
            $this->AddOperationsColumns($result);
            $this->AddFieldColumns($result);
            $this->AddSingleRecordViewColumns($result);
            $this->AddEditColumns($result);
            $this->AddMultiEditColumns($result);
            $this->AddInsertColumns($result);
            $this->AddPrintColumns($result);
            $this->AddExportColumns($result);
            $this->AddMultiUploadColumn($result);
    
    
            $this->SetShowPageList(true);
            $this->SetShowTopPageNavigator(true);
            $this->SetShowBottomPageNavigator(true);
            $this->setPrintListAvailable(true);
            $this->setPrintListRecordAvailable(false);
            $this->setPrintOneRecordAvailable(true);
            $this->setAllowPrintSelectedRecords(true);
            $this->setExportListAvailable(array('pdf', 'excel', 'word', 'xml', 'csv'));
            $this->setExportSelectedRecordsAvailable(array('pdf', 'excel', 'word', 'xml', 'csv'));
            $this->setExportListRecordAvailable(array());
            $this->setExportOneRecordAvailable(array('pdf', 'excel', 'word', 'xml', 'csv'));
    
            return $result;
        }
     
        protected function setClientSideEvents(Grid $grid) {
    
        }
    
        protected function doRegisterHandlers() {
            //
            // View column for Nome do funcionario(a) field
            //
            $column = new TextViewColumn('Nome do funcionario(a)', 'Nome do funcionario(a)', 'Nome Do Funcionario(a)', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'funcionárioGrid_Nome do funcionario(a)_handler_list', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for E-mail field
            //
            $column = new TextViewColumn('E-mail', 'E-mail', 'E-mail', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'funcionárioGrid_E-mail_handler_list', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for Nome do funcionario(a) field
            //
            $column = new TextViewColumn('Nome do funcionario(a)', 'Nome do funcionario(a)', 'Nome Do Funcionario(a)', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'funcionárioGrid_Nome do funcionario(a)_handler_print', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for Registro da Profissão field
            //
            $column = new TextViewColumn('Registro da Profissão', 'Registro da Profissão', 'Registro Da Profissão', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'funcionárioGrid_Registro da Profissão_handler_print', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for E-mail field
            //
            $column = new TextViewColumn('E-mail', 'E-mail', 'E-mail', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'funcionárioGrid_E-mail_handler_print', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for Nome do funcionario(a) field
            //
            $column = new TextViewColumn('Nome do funcionario(a)', 'Nome do funcionario(a)', 'Nome Do Funcionario(a)', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'funcionárioGrid_Nome do funcionario(a)_handler_compare', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for Registro da Profissão field
            //
            $column = new TextViewColumn('Registro da Profissão', 'Registro da Profissão', 'Registro Da Profissão', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'funcionárioGrid_Registro da Profissão_handler_compare', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for E-mail field
            //
            $column = new TextViewColumn('E-mail', 'E-mail', 'E-mail', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'funcionárioGrid_E-mail_handler_compare', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for Nome do funcionario(a) field
            //
            $column = new TextViewColumn('Nome do funcionario(a)', 'Nome do funcionario(a)', 'Nome Do Funcionario(a)', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'funcionárioGrid_Nome do funcionario(a)_handler_view', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for Registro da Profissão field
            //
            $column = new TextViewColumn('Registro da Profissão', 'Registro da Profissão', 'Registro Da Profissão', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'funcionárioGrid_Registro da Profissão_handler_view', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for E-mail field
            //
            $column = new TextViewColumn('E-mail', 'E-mail', 'E-mail', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'funcionárioGrid_E-mail_handler_view', $column);
            GetApplication()->RegisterHTTPHandler($handler);
        }
       
        protected function doCustomRenderColumn($fieldName, $fieldData, $rowData, &$customText, &$handled)
        { 
    
        }
    
        protected function doCustomRenderPrintColumn($fieldName, $fieldData, $rowData, &$customText, &$handled)
        { 
    
        }
    
        protected function doCustomRenderExportColumn($exportType, $fieldName, $fieldData, $rowData, &$customText, &$handled)
        { 
    
        }
    
        protected function doCustomDrawRow($rowData, &$cellFontColor, &$cellFontSize, &$cellBgColor, &$cellItalicAttr, &$cellBoldAttr)
        {
    
        }
    
        protected function doExtendedCustomDrawRow($rowData, &$rowCellStyles, &$rowStyles, &$rowClasses, &$cellClasses)
        {
    
        }
    
        protected function doCustomRenderTotal($totalValue, $aggregate, $columnName, &$customText, &$handled)
        {
    
        }
    
        protected function doCustomDefaultValues(&$values, &$handled) 
        {
    
        }
    
        protected function doCustomCompareColumn($columnName, $valueA, $valueB, &$result)
        {
    
        }
    
        protected function doBeforeInsertRecord($page, &$rowData, $tableName, &$cancel, &$message, &$messageDisplayTime)
        {
    
        }
    
        protected function doBeforeUpdateRecord($page, $oldRowData, &$rowData, $tableName, &$cancel, &$message, &$messageDisplayTime)
        {
    
        }
    
        protected function doBeforeDeleteRecord($page, &$rowData, $tableName, &$cancel, &$message, &$messageDisplayTime)
        {
    
        }
    
        protected function doAfterInsertRecord($page, $rowData, $tableName, &$success, &$message, &$messageDisplayTime)
        {
    
        }
    
        protected function doAfterUpdateRecord($page, $oldRowData, $rowData, $tableName, &$success, &$message, &$messageDisplayTime)
        {
    
        }
    
        protected function doAfterDeleteRecord($page, $rowData, $tableName, &$success, &$message, &$messageDisplayTime)
        {
    
        }
    
        protected function doCustomHTMLHeader($page, &$customHtmlHeaderText)
        { 
    
        }
    
        protected function doGetCustomTemplate($type, $part, $mode, &$result, &$params)
        {
    
        }
    
        protected function doGetCustomExportOptions(Page $page, $exportType, $rowData, &$options)
        {
    
        }
    
        protected function doFileUpload($fieldName, $rowData, &$result, &$accept, $originalFileName, $originalFileExtension, $fileSize, $tempFileName)
        {
    
        }
    
        protected function doPrepareChart(Chart $chart)
        {
    
        }
    
        protected function doPrepareColumnFilter(ColumnFilter $columnFilter)
        {
    
        }
    
        protected function doPrepareFilterBuilder(FilterBuilder $filterBuilder, FixedKeysArray $columns)
        {
    
        }
    
        protected function doGetSelectionFilters(FixedKeysArray $columns, &$result)
        {
    
        }
    
        protected function doGetCustomFormLayout($mode, FixedKeysArray $columns, FormLayout $layout)
        {
    
        }
    
        protected function doGetCustomColumnGroup(FixedKeysArray $columns, ViewColumnGroup $columnGroup)
        {
    
        }
    
        protected function doPageLoaded()
        {
    
        }
    
        protected function doCalculateFields($rowData, $fieldName, &$value)
        {
    
        }
    
        protected function doGetCustomPagePermissions(Page $page, PermissionSet &$permissions, &$handled)
        {
    
        }
    
        protected function doGetCustomRecordPermissions(Page $page, &$usingCondition, $rowData, &$allowEdit, &$allowDelete, &$mergeWithDefault, &$handled)
        {
    
        }
    
    }

    SetUpUserAuthorization();

    try
    {
        $Page = new funcionárioPage("funcionário", "funcionário.php", GetCurrentUserPermissionSetForDataSource("funcionário"), 'UTF-8');
        $Page->SetTitle('Funcionário');
        $Page->SetMenuLabel('Funcionário');
        $Page->SetHeader(GetPagesHeader());
        $Page->SetFooter(GetPagesFooter());
        $Page->SetRecordPermission(GetCurrentUserRecordPermissionsForDataSource("funcionário"));
        GetApplication()->SetMainPage($Page);
        GetApplication()->Run();
    }
    catch(Exception $e)
    {
        ShowErrorPage($e);
    }
	
