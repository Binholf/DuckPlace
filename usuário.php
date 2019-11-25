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
    
    
    
    class usuárioPage extends Page
    {
        protected function DoBeforeCreate()
        {
            $this->dataset = new TableDataset(
                MyPDOConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`usuário`');
            $this->dataset->addFields(
                array(
                    new IntegerField('ID', true, true, true),
                    new StringField('Tipo', true),
                    new StringField('Cliente'),
                    new StringField('Nome', true),
                    new DateField('Data de nascimento', true),
                    new StringField('CPF', true),
                    new StringField('RG', true),
                    new StringField('Contato', true),
                    new StringField('E-mail', true),
                    new StringField('Senha', true)
                )
            );
            $this->dataset->AddLookupField('Cliente', 'perfil', new IntegerField('ID'), new StringField('Nome', false, false, false, false, 'Cliente_Nome', 'Cliente_Nome_perfil'), 'Cliente_Nome_perfil');
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
                new FilterColumn($this->dataset, 'Tipo', 'Tipo', 'Tipo'),
                new FilterColumn($this->dataset, 'Cliente', 'Cliente_Nome', 'Cliente'),
                new FilterColumn($this->dataset, 'Nome', 'Nome', 'Nome'),
                new FilterColumn($this->dataset, 'Data de nascimento', 'Data de nascimento', 'Data De Nascimento'),
                new FilterColumn($this->dataset, 'CPF', 'CPF', 'CPF'),
                new FilterColumn($this->dataset, 'RG', 'RG', 'RG'),
                new FilterColumn($this->dataset, 'Contato', 'Contato', 'Contato'),
                new FilterColumn($this->dataset, 'E-mail', 'E-mail', 'E-mail'),
                new FilterColumn($this->dataset, 'Senha', 'Senha', 'Senha')
            );
        }
    
        protected function setupQuickFilter(QuickFilter $quickFilter, FixedKeysArray $columns)
        {
            $quickFilter
                ->addColumn($columns['Tipo'])
                ->addColumn($columns['Nome'])
                ->addColumn($columns['CPF'])
                ->addColumn($columns['RG']);
        }
    
        protected function setupColumnFilter(ColumnFilter $columnFilter)
        {
    
        }
    
        protected function setupFilterBuilder(FilterBuilder $filterBuilder, FixedKeysArray $columns)
        {
            $main_editor = new MultiValueSelect('Tipo');
            $main_editor->addChoice('Administrador', 'Administrador');
            $main_editor->addChoice('Responsável', 'Responsável');
            $main_editor->addChoice('Cliente', 'Cliente');
            
            $text_editor = new TextEdit('Tipo');
            
            $filterBuilder->addColumn(
                $columns['Tipo'],
                array(
                    FilterConditionOperator::EQUALS => $main_editor,
                    FilterConditionOperator::DOES_NOT_EQUAL => $main_editor,
                    FilterConditionOperator::IS_GREATER_THAN => $main_editor,
                    FilterConditionOperator::IS_GREATER_THAN_OR_EQUAL_TO => $main_editor,
                    FilterConditionOperator::IS_LESS_THAN => $main_editor,
                    FilterConditionOperator::IS_LESS_THAN_OR_EQUAL_TO => $main_editor,
                    FilterConditionOperator::CONTAINS => $text_editor,
                    FilterConditionOperator::DOES_NOT_CONTAIN => $text_editor,
                    FilterConditionOperator::BEGINS_WITH => $text_editor,
                    FilterConditionOperator::ENDS_WITH => $text_editor,
                    FilterConditionOperator::IS_LIKE => $text_editor,
                    FilterConditionOperator::IS_NOT_LIKE => $text_editor,
                    FilterConditionOperator::IS_BLANK => null,
                    FilterConditionOperator::IS_NOT_BLANK => null
                )
            );
            
            $main_editor = new TextEdit('nome_edit');
            $main_editor->SetMaxLength(100);
            
            $filterBuilder->addColumn(
                $columns['Nome'],
                array(
                    FilterConditionOperator::EQUALS => $main_editor,
                    FilterConditionOperator::DOES_NOT_EQUAL => $main_editor,
                    FilterConditionOperator::IS_GREATER_THAN => $main_editor,
                    FilterConditionOperator::IS_GREATER_THAN_OR_EQUAL_TO => $main_editor,
                    FilterConditionOperator::IS_LESS_THAN => $main_editor,
                    FilterConditionOperator::IS_LESS_THAN_OR_EQUAL_TO => $main_editor,
                    FilterConditionOperator::IS_BETWEEN => $main_editor,
                    FilterConditionOperator::IS_NOT_BETWEEN => $main_editor,
                    FilterConditionOperator::CONTAINS => $main_editor,
                    FilterConditionOperator::DOES_NOT_CONTAIN => $main_editor,
                    FilterConditionOperator::BEGINS_WITH => $main_editor,
                    FilterConditionOperator::ENDS_WITH => $main_editor,
                    FilterConditionOperator::IS_LIKE => $main_editor,
                    FilterConditionOperator::IS_NOT_LIKE => $main_editor,
                    FilterConditionOperator::IS_BLANK => null,
                    FilterConditionOperator::IS_NOT_BLANK => null
                )
            );
            
            $main_editor = new TextEdit('cpf_edit');
            $main_editor->SetMaxLength(14);
            
            $filterBuilder->addColumn(
                $columns['CPF'],
                array(
                    FilterConditionOperator::EQUALS => $main_editor,
                    FilterConditionOperator::DOES_NOT_EQUAL => $main_editor,
                    FilterConditionOperator::IS_GREATER_THAN => $main_editor,
                    FilterConditionOperator::IS_GREATER_THAN_OR_EQUAL_TO => $main_editor,
                    FilterConditionOperator::IS_LESS_THAN => $main_editor,
                    FilterConditionOperator::IS_LESS_THAN_OR_EQUAL_TO => $main_editor,
                    FilterConditionOperator::IS_BETWEEN => $main_editor,
                    FilterConditionOperator::IS_NOT_BETWEEN => $main_editor,
                    FilterConditionOperator::CONTAINS => $main_editor,
                    FilterConditionOperator::DOES_NOT_CONTAIN => $main_editor,
                    FilterConditionOperator::BEGINS_WITH => $main_editor,
                    FilterConditionOperator::ENDS_WITH => $main_editor,
                    FilterConditionOperator::IS_LIKE => $main_editor,
                    FilterConditionOperator::IS_NOT_LIKE => $main_editor,
                    FilterConditionOperator::IS_BLANK => null,
                    FilterConditionOperator::IS_NOT_BLANK => null
                )
            );
            
            $main_editor = new TextEdit('rg_edit');
            $main_editor->SetMaxLength(15);
            
            $filterBuilder->addColumn(
                $columns['RG'],
                array(
                    FilterConditionOperator::EQUALS => $main_editor,
                    FilterConditionOperator::DOES_NOT_EQUAL => $main_editor,
                    FilterConditionOperator::IS_GREATER_THAN => $main_editor,
                    FilterConditionOperator::IS_GREATER_THAN_OR_EQUAL_TO => $main_editor,
                    FilterConditionOperator::IS_LESS_THAN => $main_editor,
                    FilterConditionOperator::IS_LESS_THAN_OR_EQUAL_TO => $main_editor,
                    FilterConditionOperator::IS_BETWEEN => $main_editor,
                    FilterConditionOperator::IS_NOT_BETWEEN => $main_editor,
                    FilterConditionOperator::CONTAINS => $main_editor,
                    FilterConditionOperator::DOES_NOT_CONTAIN => $main_editor,
                    FilterConditionOperator::BEGINS_WITH => $main_editor,
                    FilterConditionOperator::ENDS_WITH => $main_editor,
                    FilterConditionOperator::IS_LIKE => $main_editor,
                    FilterConditionOperator::IS_NOT_LIKE => $main_editor,
                    FilterConditionOperator::IS_BLANK => null,
                    FilterConditionOperator::IS_NOT_BLANK => null
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
            // View column for Tipo field
            //
            $column = new TextViewColumn('Tipo', 'Tipo', 'Tipo', $this->dataset);
            $column->SetOrderable(true);
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->SetDescription('');
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for Nome field
            //
            $column = new TextViewColumn('Nome', 'Nome', 'Nome', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('usuárioGrid_Nome_handler_list');
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->SetDescription('');
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for CPF field
            //
            $column = new TextViewColumn('CPF', 'CPF', 'CPF', $this->dataset);
            $column->SetOrderable(true);
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->SetDescription('');
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for RG field
            //
            $column = new TextViewColumn('RG', 'RG', 'RG', $this->dataset);
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
            $column->SetFullTextWindowHandlerName('usuárioGrid_E-mail_handler_list');
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->SetDescription('');
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
        }
    
        protected function AddSingleRecordViewColumns(Grid $grid)
        {
            //
            // View column for Tipo field
            //
            $column = new TextViewColumn('Tipo', 'Tipo', 'Tipo', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for Nome field
            //
            $column = new TextViewColumn('Cliente', 'Cliente_Nome', 'Cliente', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('usuárioGrid_Cliente_Nome_handler_view');
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for Nome field
            //
            $column = new TextViewColumn('Nome', 'Nome', 'Nome', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('usuárioGrid_Nome_handler_view');
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for Data de nascimento field
            //
            $column = new DateTimeViewColumn('Data de nascimento', 'Data de nascimento', 'Data De Nascimento', $this->dataset);
            $column->SetOrderable(true);
            $column->SetDateTimeFormat('Y-m-d');
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for CPF field
            //
            $column = new TextViewColumn('CPF', 'CPF', 'CPF', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for RG field
            //
            $column = new TextViewColumn('RG', 'RG', 'RG', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for Contato field
            //
            $column = new TextViewColumn('Contato', 'Contato', 'Contato', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for E-mail field
            //
            $column = new TextViewColumn('E-mail', 'E-mail', 'E-mail', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('usuárioGrid_E-mail_handler_view');
            $grid->AddSingleRecordViewColumn($column);
        }
    
        protected function AddEditColumns(Grid $grid)
        {
            //
            // Edit column for Tipo field
            //
            $editor = new CheckBoxGroup('tipo_edit');
            $editor->SetDisplayMode(CheckBoxGroup::StackedMode);
            $editor->addChoice('Administrador', 'Administrador');
            $editor->addChoice('Responsável', 'Responsável');
            $editor->addChoice('Cliente', 'Cliente');
            $editColumn = new CustomEditColumn('Tipo', 'Tipo', $editor, $this->dataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for Cliente field
            //
            $editor = new DynamicCombobox('cliente_edit', $this->CreateLinkBuilder());
            $editor->setAllowClear(true);
            $editor->setMinimumInputLength(0);
            $lookupDataset = new TableDataset(
                MyPDOConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`perfil`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('ID', true, true, true),
                    new StringField('Nome', true),
                    new StringField('CPF', true),
                    new StringField('RG', true),
                    new StringField('Contato', true),
                    new StringField('Ação', true),
                    new DateField('Data', true)
                )
            );
            $lookupDataset->setOrderByField('Nome', 'ASC');
            $editColumn = new DynamicLookupEditColumn('Cliente', 'Cliente', 'Cliente_Nome', 'edit_Cliente_Nome_search', $editor, $this->dataset, $lookupDataset, 'ID', 'Nome', '');
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
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
            // Edit column for Data de nascimento field
            //
            $editor = new DateTimeEdit('data_de_nascimento_edit', false, 'Y-m-d');
            $editColumn = new CustomEditColumn('Data De Nascimento', 'Data de nascimento', $editor, $this->dataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for Contato field
            //
            $editor = new TextEdit('contato_edit');
            $editor->SetMaxLength(14);
            $editColumn = new CustomEditColumn('Contato', 'Contato', $editor, $this->dataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $validator = new MaxLengthValidator(14, StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('MaxlengthValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $validator = new MinLengthValidator(14, StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('MinlengthValidationMessage'), $editColumn->GetCaption()));
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
            $validator = new EMailValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('EmailValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for Senha field
            //
            $editor = new TextEdit('senha_edit');
            $editor->SetMaxLength(20);
            $editColumn = new CustomEditColumn('Senha', 'Senha', $editor, $this->dataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
        }
    
        protected function AddMultiEditColumns(Grid $grid)
        {
            //
            // Edit column for Tipo field
            //
            $editor = new CheckBoxGroup('tipo_edit');
            $editor->SetDisplayMode(CheckBoxGroup::StackedMode);
            $editor->addChoice('Administrador', 'Administrador');
            $editor->addChoice('Responsável', 'Responsável');
            $editor->addChoice('Cliente', 'Cliente');
            $editColumn = new CustomEditColumn('Tipo', 'Tipo', $editor, $this->dataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddMultiEditColumn($editColumn);
            
            //
            // Edit column for Cliente field
            //
            $editor = new DynamicCombobox('cliente_edit', $this->CreateLinkBuilder());
            $editor->setAllowClear(true);
            $editor->setMinimumInputLength(0);
            $lookupDataset = new TableDataset(
                MyPDOConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`perfil`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('ID', true, true, true),
                    new StringField('Nome', true),
                    new StringField('CPF', true),
                    new StringField('RG', true),
                    new StringField('Contato', true),
                    new StringField('Ação', true),
                    new DateField('Data', true)
                )
            );
            $lookupDataset->setOrderByField('Nome', 'ASC');
            $editColumn = new DynamicLookupEditColumn('Cliente', 'Cliente', 'Cliente_Nome', 'multi_edit_Cliente_Nome_search', $editor, $this->dataset, $lookupDataset, 'ID', 'Nome', '');
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddMultiEditColumn($editColumn);
            
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
            // Edit column for Data de nascimento field
            //
            $editor = new DateTimeEdit('data_de_nascimento_edit', false, 'Y-m-d');
            $editColumn = new CustomEditColumn('Data De Nascimento', 'Data de nascimento', $editor, $this->dataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddMultiEditColumn($editColumn);
            
            //
            // Edit column for Contato field
            //
            $editor = new TextEdit('contato_edit');
            $editor->SetMaxLength(14);
            $editColumn = new CustomEditColumn('Contato', 'Contato', $editor, $this->dataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $validator = new MaxLengthValidator(14, StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('MaxlengthValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $validator = new MinLengthValidator(14, StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('MinlengthValidationMessage'), $editColumn->GetCaption()));
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
            $validator = new EMailValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('EmailValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddMultiEditColumn($editColumn);
            
            //
            // Edit column for Senha field
            //
            $editor = new TextEdit('senha_edit');
            $editor->SetMaxLength(20);
            $editColumn = new CustomEditColumn('Senha', 'Senha', $editor, $this->dataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddMultiEditColumn($editColumn);
        }
    
        protected function AddInsertColumns(Grid $grid)
        {
            //
            // Edit column for Tipo field
            //
            $editor = new CheckBoxGroup('tipo_edit');
            $editor->SetDisplayMode(CheckBoxGroup::StackedMode);
            $editor->addChoice('Administrador', 'Administrador');
            $editor->addChoice('Responsável', 'Responsável');
            $editor->addChoice('Cliente', 'Cliente');
            $editColumn = new CustomEditColumn('Tipo', 'Tipo', $editor, $this->dataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for Cliente field
            //
            $editor = new DynamicCombobox('cliente_edit', $this->CreateLinkBuilder());
            $editor->setAllowClear(true);
            $editor->setMinimumInputLength(0);
            $lookupDataset = new TableDataset(
                MyPDOConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`perfil`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('ID', true, true, true),
                    new StringField('Nome', true),
                    new StringField('CPF', true),
                    new StringField('RG', true),
                    new StringField('Contato', true),
                    new StringField('Ação', true),
                    new DateField('Data', true)
                )
            );
            $lookupDataset->setOrderByField('Nome', 'ASC');
            $editColumn = new DynamicLookupEditColumn('Cliente', 'Cliente', 'Cliente_Nome', 'insert_Cliente_Nome_search', $editor, $this->dataset, $lookupDataset, 'ID', 'Nome', '');
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
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
            // Edit column for Data de nascimento field
            //
            $editor = new DateTimeEdit('data_de_nascimento_edit', false, 'Y-m-d');
            $editColumn = new CustomEditColumn('Data De Nascimento', 'Data de nascimento', $editor, $this->dataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for CPF field
            //
            $editor = new TextEdit('cpf_edit');
            $editor->SetMaxLength(14);
            $editColumn = new CustomEditColumn('CPF', 'CPF', $editor, $this->dataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $validator = new MaxLengthValidator(14, StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('MaxlengthValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $validator = new MinLengthValidator(14, StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('MinlengthValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for RG field
            //
            $editor = new TextEdit('rg_edit');
            $editor->SetMaxLength(15);
            $editColumn = new CustomEditColumn('RG', 'RG', $editor, $this->dataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $validator = new MaxLengthValidator(15, StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('MaxlengthValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $validator = new MinLengthValidator(15, StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('MinlengthValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for Contato field
            //
            $editor = new TextEdit('contato_edit');
            $editor->SetMaxLength(14);
            $editColumn = new CustomEditColumn('Contato', 'Contato', $editor, $this->dataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $validator = new MaxLengthValidator(14, StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('MaxlengthValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $validator = new MinLengthValidator(14, StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('MinlengthValidationMessage'), $editColumn->GetCaption()));
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
            $validator = new EMailValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('EmailValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for Senha field
            //
            $editor = new TextEdit('senha_edit');
            $editor->SetMaxLength(20);
            $editColumn = new CustomEditColumn('Senha', 'Senha', $editor, $this->dataset);
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
            // View column for Tipo field
            //
            $column = new TextViewColumn('Tipo', 'Tipo', 'Tipo', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for Nome field
            //
            $column = new TextViewColumn('Cliente', 'Cliente_Nome', 'Cliente', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('usuárioGrid_Cliente_Nome_handler_print');
            $grid->AddPrintColumn($column);
            
            //
            // View column for Nome field
            //
            $column = new TextViewColumn('Nome', 'Nome', 'Nome', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('usuárioGrid_Nome_handler_print');
            $grid->AddPrintColumn($column);
            
            //
            // View column for Data de nascimento field
            //
            $column = new DateTimeViewColumn('Data de nascimento', 'Data de nascimento', 'Data De Nascimento', $this->dataset);
            $column->SetOrderable(true);
            $column->SetDateTimeFormat('Y-m-d');
            $grid->AddPrintColumn($column);
            
            //
            // View column for CPF field
            //
            $column = new TextViewColumn('CPF', 'CPF', 'CPF', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for RG field
            //
            $column = new TextViewColumn('RG', 'RG', 'RG', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for Contato field
            //
            $column = new TextViewColumn('Contato', 'Contato', 'Contato', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for E-mail field
            //
            $column = new TextViewColumn('E-mail', 'E-mail', 'E-mail', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('usuárioGrid_E-mail_handler_print');
            $grid->AddPrintColumn($column);
            
            //
            // View column for Senha field
            //
            $column = new TextViewColumn('Senha', 'Senha', 'Senha', $this->dataset);
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
            // View column for Tipo field
            //
            $column = new TextViewColumn('Tipo', 'Tipo', 'Tipo', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for Nome field
            //
            $column = new TextViewColumn('Cliente', 'Cliente_Nome', 'Cliente', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('usuárioGrid_Cliente_Nome_handler_export');
            $grid->AddExportColumn($column);
            
            //
            // View column for Nome field
            //
            $column = new TextViewColumn('Nome', 'Nome', 'Nome', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('usuárioGrid_Nome_handler_export');
            $grid->AddExportColumn($column);
            
            //
            // View column for Data de nascimento field
            //
            $column = new DateTimeViewColumn('Data de nascimento', 'Data de nascimento', 'Data De Nascimento', $this->dataset);
            $column->SetOrderable(true);
            $column->SetDateTimeFormat('Y-m-d');
            $grid->AddExportColumn($column);
            
            //
            // View column for CPF field
            //
            $column = new TextViewColumn('CPF', 'CPF', 'CPF', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for RG field
            //
            $column = new TextViewColumn('RG', 'RG', 'RG', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for Contato field
            //
            $column = new TextViewColumn('Contato', 'Contato', 'Contato', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for E-mail field
            //
            $column = new TextViewColumn('E-mail', 'E-mail', 'E-mail', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('usuárioGrid_E-mail_handler_export');
            $grid->AddExportColumn($column);
            
            //
            // View column for Senha field
            //
            $column = new TextViewColumn('Senha', 'Senha', 'Senha', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
        }
    
        private function AddCompareColumns(Grid $grid)
        {
            //
            // View column for Tipo field
            //
            $column = new TextViewColumn('Tipo', 'Tipo', 'Tipo', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddCompareColumn($column);
            
            //
            // View column for Nome field
            //
            $column = new TextViewColumn('Cliente', 'Cliente_Nome', 'Cliente', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('usuárioGrid_Cliente_Nome_handler_compare');
            $grid->AddCompareColumn($column);
            
            //
            // View column for Nome field
            //
            $column = new TextViewColumn('Nome', 'Nome', 'Nome', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('usuárioGrid_Nome_handler_compare');
            $grid->AddCompareColumn($column);
            
            //
            // View column for Data de nascimento field
            //
            $column = new DateTimeViewColumn('Data de nascimento', 'Data de nascimento', 'Data De Nascimento', $this->dataset);
            $column->SetOrderable(true);
            $column->SetDateTimeFormat('Y-m-d');
            $grid->AddCompareColumn($column);
            
            //
            // View column for CPF field
            //
            $column = new TextViewColumn('CPF', 'CPF', 'CPF', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddCompareColumn($column);
            
            //
            // View column for RG field
            //
            $column = new TextViewColumn('RG', 'RG', 'RG', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddCompareColumn($column);
            
            //
            // View column for Contato field
            //
            $column = new TextViewColumn('Contato', 'Contato', 'Contato', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddCompareColumn($column);
            
            //
            // View column for E-mail field
            //
            $column = new TextViewColumn('E-mail', 'E-mail', 'E-mail', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('usuárioGrid_E-mail_handler_compare');
            $grid->AddCompareColumn($column);
            
            //
            // View column for Senha field
            //
            $column = new TextViewColumn('Senha', 'Senha', 'Senha', $this->dataset);
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
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'usuárioGrid_Nome_handler_list', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for E-mail field
            //
            $column = new TextViewColumn('E-mail', 'E-mail', 'E-mail', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'usuárioGrid_E-mail_handler_list', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for Nome field
            //
            $column = new TextViewColumn('Cliente', 'Cliente_Nome', 'Cliente', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'usuárioGrid_Cliente_Nome_handler_print', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for Nome field
            //
            $column = new TextViewColumn('Nome', 'Nome', 'Nome', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'usuárioGrid_Nome_handler_print', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for E-mail field
            //
            $column = new TextViewColumn('E-mail', 'E-mail', 'E-mail', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'usuárioGrid_E-mail_handler_print', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for Nome field
            //
            $column = new TextViewColumn('Cliente', 'Cliente_Nome', 'Cliente', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'usuárioGrid_Cliente_Nome_handler_compare', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for Nome field
            //
            $column = new TextViewColumn('Nome', 'Nome', 'Nome', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'usuárioGrid_Nome_handler_compare', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for E-mail field
            //
            $column = new TextViewColumn('E-mail', 'E-mail', 'E-mail', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'usuárioGrid_E-mail_handler_compare', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            $lookupDataset = new TableDataset(
                MyPDOConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`perfil`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('ID', true, true, true),
                    new StringField('Nome', true),
                    new StringField('CPF', true),
                    new StringField('RG', true),
                    new StringField('Contato', true),
                    new StringField('Ação', true),
                    new DateField('Data', true)
                )
            );
            $lookupDataset->setOrderByField('Nome', 'ASC');
            $handler = new DynamicSearchHandler($lookupDataset, $this, 'insert_Cliente_Nome_search', 'ID', 'Nome', null, 20);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for Nome field
            //
            $column = new TextViewColumn('Cliente', 'Cliente_Nome', 'Cliente', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'usuárioGrid_Cliente_Nome_handler_view', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for Nome field
            //
            $column = new TextViewColumn('Nome', 'Nome', 'Nome', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'usuárioGrid_Nome_handler_view', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for E-mail field
            //
            $column = new TextViewColumn('E-mail', 'E-mail', 'E-mail', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'usuárioGrid_E-mail_handler_view', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            $lookupDataset = new TableDataset(
                MyPDOConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`perfil`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('ID', true, true, true),
                    new StringField('Nome', true),
                    new StringField('CPF', true),
                    new StringField('RG', true),
                    new StringField('Contato', true),
                    new StringField('Ação', true),
                    new DateField('Data', true)
                )
            );
            $lookupDataset->setOrderByField('Nome', 'ASC');
            $handler = new DynamicSearchHandler($lookupDataset, $this, 'edit_Cliente_Nome_search', 'ID', 'Nome', null, 20);
            GetApplication()->RegisterHTTPHandler($handler);
            
            $lookupDataset = new TableDataset(
                MyPDOConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`perfil`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('ID', true, true, true),
                    new StringField('Nome', true),
                    new StringField('CPF', true),
                    new StringField('RG', true),
                    new StringField('Contato', true),
                    new StringField('Ação', true),
                    new DateField('Data', true)
                )
            );
            $lookupDataset->setOrderByField('Nome', 'ASC');
            $handler = new DynamicSearchHandler($lookupDataset, $this, 'multi_edit_Cliente_Nome_search', 'ID', 'Nome', null, 20);
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
        $Page = new usuárioPage("usuário", "usuário.php", GetCurrentUserPermissionSetForDataSource("usuário"), 'UTF-8');
        $Page->SetTitle('Usuário');
        $Page->SetMenuLabel('Usuário');
        $Page->SetHeader(GetPagesHeader());
        $Page->SetFooter(GetPagesFooter());
        $Page->SetRecordPermission(GetCurrentUserRecordPermissionsForDataSource("usuário"));
        GetApplication()->SetMainPage($Page);
        GetApplication()->Run();
    }
    catch(Exception $e)
    {
        ShowErrorPage($e);
    }
	
