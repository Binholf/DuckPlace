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
    
    
    
    class animalPage extends Page
    {
        protected function DoBeforeCreate()
        {
            $this->dataset = new TableDataset(
                MyPDOConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`animal`');
            $this->dataset->addFields(
                array(
                    new IntegerField('ID', true, true, true),
                    new StringField('Nome', true),
                    new StringField('Espécie', true),
                    new StringField('Descrição', true),
                    new StringField('Vacinação', true),
                    new StringField('Idade', true),
                    new StringField('Responsável', true),
                    new StringField('Status', true)
                )
            );
            $this->dataset->AddLookupField('Espécie', 'especie', new IntegerField('ID'), new StringField('Nome', false, false, false, false, 'Espécie_Nome', 'Espécie_Nome_especie'), 'Espécie_Nome_especie');
            $this->dataset->AddLookupField('Vacinação', 'vacina', new IntegerField('ID'), new StringField('Nome', false, false, false, false, 'Vacinação_Nome', 'Vacinação_Nome_vacina'), 'Vacinação_Nome_vacina');
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
                new FilterColumn($this->dataset, 'Nome', 'Nome', 'Nome'),
                new FilterColumn($this->dataset, 'Espécie', 'Espécie_Nome', 'Espécie'),
                new FilterColumn($this->dataset, 'Vacinação', 'Vacinação_Nome', 'Vacinação'),
                new FilterColumn($this->dataset, 'Idade', 'Idade', 'Idade'),
                new FilterColumn($this->dataset, 'Responsável', 'Responsável', 'Responsável'),
                new FilterColumn($this->dataset, 'Status', 'Status', 'Status'),
                new FilterColumn($this->dataset, 'Descrição', 'Descrição', 'Descrição')
            );
        }
    
        protected function setupQuickFilter(QuickFilter $quickFilter, FixedKeysArray $columns)
        {
            $quickFilter
                ->addColumn($columns['Nome'])
                ->addColumn($columns['Espécie'])
                ->addColumn($columns['Idade'])
                ->addColumn($columns['Responsável'])
                ->addColumn($columns['Status']);
        }
    
        protected function setupColumnFilter(ColumnFilter $columnFilter)
        {
            $columnFilter
                ->setOptionsFor('Espécie');
        }
    
        protected function setupFilterBuilder(FilterBuilder $filterBuilder, FixedKeysArray $columns)
        {
            $main_editor = new TextEdit('nome_edit');
            $main_editor->SetMaxLength(100);
            
            $filterBuilder->addColumn(
                $columns['Nome'],
                array(
                    FilterConditionOperator::EQUALS => $main_editor,
                    FilterConditionOperator::DOES_NOT_EQUAL => $main_editor
                )
            );
            
            $main_editor = new DynamicCombobox('espécie_edit', $this->CreateLinkBuilder());
            $main_editor->setAllowClear(true);
            $main_editor->setMinimumInputLength(0);
            $main_editor->SetAllowNullValue(false);
            $main_editor->SetHandlerName('filter_builder_Espécie_Nome_search');
            
            $multi_value_select_editor = new RemoteMultiValueSelect('Espécie', $this->CreateLinkBuilder());
            $multi_value_select_editor->SetHandlerName('filter_builder_Espécie_Nome_search');
            
            $filterBuilder->addColumn(
                $columns['Espécie'],
                array(
                    FilterConditionOperator::EQUALS => $main_editor,
                    FilterConditionOperator::DOES_NOT_EQUAL => $main_editor,
                    FilterConditionOperator::IN => $multi_value_select_editor,
                    FilterConditionOperator::NOT_IN => $multi_value_select_editor
                )
            );
            
            $main_editor = new TextEdit('idade_edit');
            
            $filterBuilder->addColumn(
                $columns['Idade'],
                array(
                    FilterConditionOperator::EQUALS => $main_editor,
                    FilterConditionOperator::DOES_NOT_EQUAL => $main_editor
                )
            );
            
            $main_editor = new TextEdit('responsável_edit');
            $main_editor->SetMaxLength(100);
            
            $filterBuilder->addColumn(
                $columns['Responsável'],
                array(
                    FilterConditionOperator::EQUALS => $main_editor,
                    FilterConditionOperator::DOES_NOT_EQUAL => $main_editor
                )
            );
            
            $main_editor = new ComboBox('Status');
            $main_editor->SetAllowNullValue(false);
            $main_editor->addChoice('Aguardando', 'Aguardando');
            $main_editor->addChoice('Adotado', 'Adotado');
            $main_editor->addChoice('Indisponivel', 'Indisponivel');
            
            $multi_value_select_editor = new MultiValueSelect('Status');
            $multi_value_select_editor->setChoices($main_editor->getChoices());
            
            $filterBuilder->addColumn(
                $columns['Status'],
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
            // View column for Nome field
            //
            $column = new TextViewColumn('Nome', 'Nome', 'Nome', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('animalGrid_Nome_handler_list');
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->SetDescription('');
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for Nome field
            //
            $column = new TextViewColumn('Espécie', 'Espécie_Nome', 'Espécie', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('animalGrid_Espécie_Nome_handler_list');
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->SetDescription('');
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for Idade field
            //
            $column = new NumberViewColumn('Idade', 'Idade', 'Idade', $this->dataset);
            $column->SetOrderable(true);
            $column->setNumberAfterDecimal(0);
            $column->setThousandsSeparator(',');
            $column->setDecimalSeparator('');
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->SetDescription('');
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for Responsável field
            //
            $column = new TextViewColumn('Responsável', 'Responsável', 'Responsável', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('animalGrid_Responsável_handler_list');
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->SetDescription('');
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for Status field
            //
            $column = new TextViewColumn('Status', 'Status', 'Status', $this->dataset);
            $column->SetOrderable(true);
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->SetDescription('');
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
        }
    
        protected function AddSingleRecordViewColumns(Grid $grid)
        {
            //
            // View column for Nome field
            //
            $column = new TextViewColumn('Nome', 'Nome', 'Nome', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('animalGrid_Nome_handler_view');
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for Nome field
            //
            $column = new TextViewColumn('Espécie', 'Espécie_Nome', 'Espécie', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('animalGrid_Espécie_Nome_handler_view');
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for Nome field
            //
            $column = new TextViewColumn('Vacinação', 'Vacinação_Nome', 'Vacinação', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('animalGrid_Vacinação_Nome_handler_view');
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for Idade field
            //
            $column = new NumberViewColumn('Idade', 'Idade', 'Idade', $this->dataset);
            $column->SetOrderable(true);
            $column->setNumberAfterDecimal(0);
            $column->setThousandsSeparator(',');
            $column->setDecimalSeparator('');
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for Responsável field
            //
            $column = new TextViewColumn('Responsável', 'Responsável', 'Responsável', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('animalGrid_Responsável_handler_view');
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for Status field
            //
            $column = new TextViewColumn('Status', 'Status', 'Status', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for Descrição field
            //
            $column = new TextViewColumn('Descrição', 'Descrição', 'Descrição', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
        }
    
        protected function AddEditColumns(Grid $grid)
        {
            //
            // Edit column for Nome field
            //
            $editor = new TextEdit('nome_edit');
            $editor->SetMaxLength(100);
            $editColumn = new CustomEditColumn('Nome', 'Nome', $editor, $this->dataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for Vacinação field
            //
            $editor = new DynamicCombobox('vacinação_edit', $this->CreateLinkBuilder());
            $editor->setAllowClear(true);
            $editor->setMinimumInputLength(0);
            $lookupDataset = new TableDataset(
                MyPDOConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`vacina`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('ID', true, true, true),
                    new StringField('Nome', true),
                    new StringField('Descrição', true),
                    new StringField('Doença', true),
                    new StringField('Idade', true)
                )
            );
            $lookupDataset->setOrderByField('Nome', 'ASC');
            $editColumn = new DynamicLookupEditColumn('Vacinação', 'Vacinação', 'Vacinação_Nome', 'edit_Vacinação_Nome_search', $editor, $this->dataset, $lookupDataset, 'ID', 'Nome', '');
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for Idade field
            //
            $editor = new TextEdit('idade_edit');
            $editColumn = new CustomEditColumn('Idade', 'Idade', $editor, $this->dataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for Status field
            //
            $editor = new RadioEdit('status_edit');
            $editor->SetDisplayMode(RadioEdit::StackedMode);
            $editor->addChoice('Aguardando', 'Aguardando');
            $editor->addChoice('Adotado', 'Adotado');
            $editor->addChoice('Indisponivel', 'Indisponivel');
            $editColumn = new CustomEditColumn('Status', 'Status', $editor, $this->dataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for Descrição field
            //
            $editor = new HtmlWysiwygEditor('descrição_edit');
            $editColumn = new CustomEditColumn('Descrição', 'Descrição', $editor, $this->dataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
        }
    
        protected function AddMultiEditColumns(Grid $grid)
        {
            //
            // Edit column for Nome field
            //
            $editor = new TextEdit('nome_edit');
            $editor->SetMaxLength(100);
            $editColumn = new CustomEditColumn('Nome', 'Nome', $editor, $this->dataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddMultiEditColumn($editColumn);
            
            //
            // Edit column for Vacinação field
            //
            $editor = new DynamicCombobox('vacinação_edit', $this->CreateLinkBuilder());
            $editor->setAllowClear(true);
            $editor->setMinimumInputLength(0);
            $lookupDataset = new TableDataset(
                MyPDOConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`vacina`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('ID', true, true, true),
                    new StringField('Nome', true),
                    new StringField('Descrição', true),
                    new StringField('Doença', true),
                    new StringField('Idade', true)
                )
            );
            $lookupDataset->setOrderByField('Nome', 'ASC');
            $editColumn = new DynamicLookupEditColumn('Vacinação', 'Vacinação', 'Vacinação_Nome', 'multi_edit_Vacinação_Nome_search', $editor, $this->dataset, $lookupDataset, 'ID', 'Nome', '');
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddMultiEditColumn($editColumn);
            
            //
            // Edit column for Idade field
            //
            $editor = new TextEdit('idade_edit');
            $editColumn = new CustomEditColumn('Idade', 'Idade', $editor, $this->dataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddMultiEditColumn($editColumn);
            
            //
            // Edit column for Status field
            //
            $editor = new RadioEdit('status_edit');
            $editor->SetDisplayMode(RadioEdit::StackedMode);
            $editor->addChoice('Aguardando', 'Aguardando');
            $editor->addChoice('Adotado', 'Adotado');
            $editor->addChoice('Indisponivel', 'Indisponivel');
            $editColumn = new CustomEditColumn('Status', 'Status', $editor, $this->dataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddMultiEditColumn($editColumn);
            
            //
            // Edit column for Descrição field
            //
            $editor = new HtmlWysiwygEditor('descrição_edit');
            $editColumn = new CustomEditColumn('Descrição', 'Descrição', $editor, $this->dataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddMultiEditColumn($editColumn);
        }
    
        protected function AddInsertColumns(Grid $grid)
        {
            //
            // Edit column for Nome field
            //
            $editor = new TextEdit('nome_edit');
            $editor->SetMaxLength(100);
            $editColumn = new CustomEditColumn('Nome', 'Nome', $editor, $this->dataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for Espécie field
            //
            $editor = new DynamicCombobox('espécie_edit', $this->CreateLinkBuilder());
            $editor->setAllowClear(true);
            $editor->setMinimumInputLength(0);
            $lookupDataset = new TableDataset(
                MyPDOConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`especie`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('ID', true, true, true),
                    new StringField('Nome', true),
                    new StringField('Nome Científico', true),
                    new StringField('Descrição', true),
                    new StringField('Classificação', true)
                )
            );
            $lookupDataset->setOrderByField('Nome', 'ASC');
            $editColumn = new DynamicLookupEditColumn('Espécie', 'Espécie', 'Espécie_Nome', 'insert_Espécie_Nome_search', $editor, $this->dataset, $lookupDataset, 'ID', 'Nome', '');
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for Vacinação field
            //
            $editor = new DynamicCombobox('vacinação_edit', $this->CreateLinkBuilder());
            $editor->setAllowClear(true);
            $editor->setMinimumInputLength(0);
            $lookupDataset = new TableDataset(
                MyPDOConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`vacina`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('ID', true, true, true),
                    new StringField('Nome', true),
                    new StringField('Descrição', true),
                    new StringField('Doença', true),
                    new StringField('Idade', true)
                )
            );
            $lookupDataset->setOrderByField('Nome', 'ASC');
            $editColumn = new DynamicLookupEditColumn('Vacinação', 'Vacinação', 'Vacinação_Nome', 'insert_Vacinação_Nome_search', $editor, $this->dataset, $lookupDataset, 'ID', 'Nome', '');
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for Idade field
            //
            $editor = new TextEdit('idade_edit');
            $editColumn = new CustomEditColumn('Idade', 'Idade', $editor, $this->dataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for Responsável field
            //
            $editor = new TextEdit('responsável_edit');
            $editor->SetMaxLength(100);
            $editColumn = new CustomEditColumn('Responsável', 'Responsável', $editor, $this->dataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for Status field
            //
            $editor = new RadioEdit('status_edit');
            $editor->SetDisplayMode(RadioEdit::StackedMode);
            $editor->addChoice('Aguardando', 'Aguardando');
            $editor->addChoice('Adotado', 'Adotado');
            $editor->addChoice('Indisponivel', 'Indisponivel');
            $editColumn = new CustomEditColumn('Status', 'Status', $editor, $this->dataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for Descrição field
            //
            $editor = new HtmlWysiwygEditor('descrição_edit');
            $editColumn = new CustomEditColumn('Descrição', 'Descrição', $editor, $this->dataset);
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
            // View column for Nome field
            //
            $column = new TextViewColumn('Nome', 'Nome', 'Nome', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('animalGrid_Nome_handler_print');
            $grid->AddPrintColumn($column);
            
            //
            // View column for Nome field
            //
            $column = new TextViewColumn('Espécie', 'Espécie_Nome', 'Espécie', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('animalGrid_Espécie_Nome_handler_print');
            $grid->AddPrintColumn($column);
            
            //
            // View column for Nome field
            //
            $column = new TextViewColumn('Vacinação', 'Vacinação_Nome', 'Vacinação', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('animalGrid_Vacinação_Nome_handler_print');
            $grid->AddPrintColumn($column);
            
            //
            // View column for Idade field
            //
            $column = new NumberViewColumn('Idade', 'Idade', 'Idade', $this->dataset);
            $column->SetOrderable(true);
            $column->setNumberAfterDecimal(0);
            $column->setThousandsSeparator(',');
            $column->setDecimalSeparator('');
            $grid->AddPrintColumn($column);
            
            //
            // View column for Responsável field
            //
            $column = new TextViewColumn('Responsável', 'Responsável', 'Responsável', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('animalGrid_Responsável_handler_print');
            $grid->AddPrintColumn($column);
            
            //
            // View column for Status field
            //
            $column = new TextViewColumn('Status', 'Status', 'Status', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for Descrição field
            //
            $column = new TextViewColumn('Descrição', 'Descrição', 'Descrição', $this->dataset);
            $column->SetOrderable(true);
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
            // View column for Nome field
            //
            $column = new TextViewColumn('Nome', 'Nome', 'Nome', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('animalGrid_Nome_handler_export');
            $grid->AddExportColumn($column);
            
            //
            // View column for Nome field
            //
            $column = new TextViewColumn('Espécie', 'Espécie_Nome', 'Espécie', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('animalGrid_Espécie_Nome_handler_export');
            $grid->AddExportColumn($column);
            
            //
            // View column for Nome field
            //
            $column = new TextViewColumn('Vacinação', 'Vacinação_Nome', 'Vacinação', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('animalGrid_Vacinação_Nome_handler_export');
            $grid->AddExportColumn($column);
            
            //
            // View column for Idade field
            //
            $column = new NumberViewColumn('Idade', 'Idade', 'Idade', $this->dataset);
            $column->SetOrderable(true);
            $column->setNumberAfterDecimal(0);
            $column->setThousandsSeparator(',');
            $column->setDecimalSeparator('');
            $grid->AddExportColumn($column);
            
            //
            // View column for Responsável field
            //
            $column = new TextViewColumn('Responsável', 'Responsável', 'Responsável', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('animalGrid_Responsável_handler_export');
            $grid->AddExportColumn($column);
            
            //
            // View column for Status field
            //
            $column = new TextViewColumn('Status', 'Status', 'Status', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for Descrição field
            //
            $column = new TextViewColumn('Descrição', 'Descrição', 'Descrição', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
        }
    
        private function AddCompareColumns(Grid $grid)
        {
            //
            // View column for Nome field
            //
            $column = new TextViewColumn('Nome', 'Nome', 'Nome', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('animalGrid_Nome_handler_compare');
            $grid->AddCompareColumn($column);
            
            //
            // View column for Nome field
            //
            $column = new TextViewColumn('Espécie', 'Espécie_Nome', 'Espécie', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('animalGrid_Espécie_Nome_handler_compare');
            $grid->AddCompareColumn($column);
            
            //
            // View column for Nome field
            //
            $column = new TextViewColumn('Vacinação', 'Vacinação_Nome', 'Vacinação', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('animalGrid_Vacinação_Nome_handler_compare');
            $grid->AddCompareColumn($column);
            
            //
            // View column for Idade field
            //
            $column = new NumberViewColumn('Idade', 'Idade', 'Idade', $this->dataset);
            $column->SetOrderable(true);
            $column->setNumberAfterDecimal(0);
            $column->setThousandsSeparator(',');
            $column->setDecimalSeparator('');
            $grid->AddCompareColumn($column);
            
            //
            // View column for Responsável field
            //
            $column = new TextViewColumn('Responsável', 'Responsável', 'Responsável', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('animalGrid_Responsável_handler_compare');
            $grid->AddCompareColumn($column);
            
            //
            // View column for Status field
            //
            $column = new TextViewColumn('Status', 'Status', 'Status', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddCompareColumn($column);
            
            //
            // View column for Descrição field
            //
            $column = new TextViewColumn('Descrição', 'Descrição', 'Descrição', $this->dataset);
            $column->SetOrderable(true);
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
            // View column for Nome field
            //
            $column = new TextViewColumn('Nome', 'Nome', 'Nome', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'animalGrid_Nome_handler_list', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for Nome field
            //
            $column = new TextViewColumn('Espécie', 'Espécie_Nome', 'Espécie', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'animalGrid_Espécie_Nome_handler_list', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for Responsável field
            //
            $column = new TextViewColumn('Responsável', 'Responsável', 'Responsável', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'animalGrid_Responsável_handler_list', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for Nome field
            //
            $column = new TextViewColumn('Nome', 'Nome', 'Nome', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'animalGrid_Nome_handler_print', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for Nome field
            //
            $column = new TextViewColumn('Espécie', 'Espécie_Nome', 'Espécie', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'animalGrid_Espécie_Nome_handler_print', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for Nome field
            //
            $column = new TextViewColumn('Vacinação', 'Vacinação_Nome', 'Vacinação', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'animalGrid_Vacinação_Nome_handler_print', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for Responsável field
            //
            $column = new TextViewColumn('Responsável', 'Responsável', 'Responsável', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'animalGrid_Responsável_handler_print', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for Nome field
            //
            $column = new TextViewColumn('Nome', 'Nome', 'Nome', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'animalGrid_Nome_handler_compare', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for Nome field
            //
            $column = new TextViewColumn('Espécie', 'Espécie_Nome', 'Espécie', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'animalGrid_Espécie_Nome_handler_compare', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for Nome field
            //
            $column = new TextViewColumn('Vacinação', 'Vacinação_Nome', 'Vacinação', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'animalGrid_Vacinação_Nome_handler_compare', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for Responsável field
            //
            $column = new TextViewColumn('Responsável', 'Responsável', 'Responsável', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'animalGrid_Responsável_handler_compare', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            $lookupDataset = new TableDataset(
                MyPDOConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`especie`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('ID', true, true, true),
                    new StringField('Nome', true),
                    new StringField('Nome Científico', true),
                    new StringField('Descrição', true),
                    new StringField('Classificação', true)
                )
            );
            $lookupDataset->setOrderByField('Nome', 'ASC');
            $handler = new DynamicSearchHandler($lookupDataset, $this, 'insert_Espécie_Nome_search', 'ID', 'Nome', null, 20);
            GetApplication()->RegisterHTTPHandler($handler);
            
            $lookupDataset = new TableDataset(
                MyPDOConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`vacina`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('ID', true, true, true),
                    new StringField('Nome', true),
                    new StringField('Descrição', true),
                    new StringField('Doença', true),
                    new StringField('Idade', true)
                )
            );
            $lookupDataset->setOrderByField('Nome', 'ASC');
            $handler = new DynamicSearchHandler($lookupDataset, $this, 'insert_Vacinação_Nome_search', 'ID', 'Nome', null, 20);
            GetApplication()->RegisterHTTPHandler($handler);
            
            $lookupDataset = new TableDataset(
                MyPDOConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`especie`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('ID', true, true, true),
                    new StringField('Nome', true),
                    new StringField('Nome Científico', true),
                    new StringField('Descrição', true),
                    new StringField('Classificação', true)
                )
            );
            $lookupDataset->setOrderByField('Nome', 'ASC');
            $handler = new DynamicSearchHandler($lookupDataset, $this, 'filter_builder_Espécie_Nome_search', 'ID', 'Nome', null, 20);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for Nome field
            //
            $column = new TextViewColumn('Nome', 'Nome', 'Nome', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'animalGrid_Nome_handler_view', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for Nome field
            //
            $column = new TextViewColumn('Espécie', 'Espécie_Nome', 'Espécie', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'animalGrid_Espécie_Nome_handler_view', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for Nome field
            //
            $column = new TextViewColumn('Vacinação', 'Vacinação_Nome', 'Vacinação', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'animalGrid_Vacinação_Nome_handler_view', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for Responsável field
            //
            $column = new TextViewColumn('Responsável', 'Responsável', 'Responsável', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'animalGrid_Responsável_handler_view', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            $lookupDataset = new TableDataset(
                MyPDOConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`vacina`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('ID', true, true, true),
                    new StringField('Nome', true),
                    new StringField('Descrição', true),
                    new StringField('Doença', true),
                    new StringField('Idade', true)
                )
            );
            $lookupDataset->setOrderByField('Nome', 'ASC');
            $handler = new DynamicSearchHandler($lookupDataset, $this, 'edit_Vacinação_Nome_search', 'ID', 'Nome', null, 20);
            GetApplication()->RegisterHTTPHandler($handler);
            
            $lookupDataset = new TableDataset(
                MyPDOConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`vacina`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('ID', true, true, true),
                    new StringField('Nome', true),
                    new StringField('Descrição', true),
                    new StringField('Doença', true),
                    new StringField('Idade', true)
                )
            );
            $lookupDataset->setOrderByField('Nome', 'ASC');
            $handler = new DynamicSearchHandler($lookupDataset, $this, 'multi_edit_Vacinação_Nome_search', 'ID', 'Nome', null, 20);
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
        $Page = new animalPage("animal", "animal.php", GetCurrentUserPermissionSetForDataSource("animal"), 'UTF-8');
        $Page->SetTitle('Animal');
        $Page->SetMenuLabel('Animal');
        $Page->SetHeader(GetPagesHeader());
        $Page->SetFooter(GetPagesFooter());
        $Page->SetRecordPermission(GetCurrentUserRecordPermissionsForDataSource("animal"));
        GetApplication()->SetMainPage($Page);
        GetApplication()->Run();
    }
    catch(Exception $e)
    {
        ShowErrorPage($e);
    }
	
