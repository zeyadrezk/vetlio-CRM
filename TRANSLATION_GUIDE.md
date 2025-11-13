# Translation Guide for Vetlio CRM

This guide explains how to use the multi-language translation system in your Filament resources, pages, and components.

## Translation File Structure

```
lang/
├── en/                         # English translations
│   ├── common.php             # Common fields, actions, messages
│   ├── navigation.php         # Navigation labels and groups
│   ├── filament.php           # Filament UI translations
│   ├── enums.php              # Enum translations
│   ├── tables.php             # Table-related translations
│   ├── resources/             # Resource-specific translations
│   │   ├── clients.php
│   │   ├── patients.php
│   │   ├── invoices.php
│   │   ├── reservations.php
│   │   └── email.php
│   └── ...                    # Laravel default translation files
│
└── ar/                         # Arabic translations (same structure)
    └── ...
```

## Usage Examples

### 1. Navigation Labels in Resources

**Before:**
```php
class ClientResource extends Resource
{
    protected static ?string $navigationLabel = 'Clients';
    protected static ?string $label = 'client';
    protected static ?string $pluralLabel = 'clients';
    protected static ?string $navigationGroup = 'Finance';
}
```

**After:**
```php
class ClientResource extends Resource
{
    protected static ?string $navigationLabel = null;
    protected static ?string $label = null;
    protected static ?string $pluralLabel = null;
    protected static ?string $navigationGroup = null;

    public static function getNavigationLabel(): string
    {
        return __('resources/clients.navigation');
    }

    public static function getLabel(): ?string
    {
        return __('resources/clients.singular');
    }

    public static function getPluralLabel(): ?string
    {
        return __('resources/clients.plural');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.groups.finance');
    }
}
```

### 2. Table Column Labels

**Before:**
```php
TextColumn::make('full_name')
    ->label('Full name'),

TextColumn::make('email')
    ->label('Email'),

TextColumn::make('phone')
    ->label('Phone'),
```

**After:**
```php
TextColumn::make('full_name')
    ->label(__('resources/clients.fields.full_name')),

// For common fields, use common.php
TextColumn::make('email')
    ->label(__('common.fields.email')),

TextColumn::make('phone')
    ->label(__('common.fields.phone')),
```

### 3. Form Field Labels

**Before:**
```php
TextInput::make('first_name')
    ->label('First name')
    ->required(),

Select::make('gender')
    ->label('Gender')
    ->options([
        'male' => 'Male',
        'female' => 'Female',
        'unspecified' => 'Unspecified',
    ]),
```

**After:**
```php
TextInput::make('first_name')
    ->label(__('resources/clients.fields.first_name'))
    ->required(),

Select::make('gender')
    ->label(__('resources/clients.fields.gender'))
    ->options([
        'male' => __('common.gender.male'),
        'female' => __('common.gender.female'),
        'unspecified' => __('common.gender.unspecified'),
    ]),
```

### 4. Form Tabs

**Before:**
```php
Tabs::make('Tabs')
    ->tabs([
        Tabs\Tab::make('Basic information')
            ->schema([...]),

        Tabs\Tab::make('Address')
            ->schema([...]),
    ]);
```

**After:**
```php
Tabs::make('Tabs')
    ->tabs([
        Tabs\Tab::make(__('resources/clients.tabs.basic_information'))
            ->schema([...]),

        Tabs\Tab::make(__('resources/clients.tabs.address'))
            ->schema([...]),
    ]);
```

### 5. Actions

**Before:**
```php
Action::make('send_email')
    ->label('Send email')
    ->successNotificationTitle('Email sent successfully')
    ->failureNotificationTitle('Error sending email'),
```

**After:**
```php
Action::make('send_email')
    ->label(__('resources/email.send_email.title'))
    ->successNotificationTitle(__('resources/email.send_email.success'))
    ->failureNotificationTitle(__('resources/email.send_email.error')),
```

### 6. Validation Messages

**Before:**
```php
TextInput::make('email')
    ->label('Email')
    ->unique(ignoreRecord: true)
    ->validationMessages([
        'unique' => 'Email address is already in use.',
    ]),
```

**After:**
```php
TextInput::make('email')
    ->label(__('common.fields.email'))
    ->unique(ignoreRecord: true)
    ->validationMessages([
        'unique' => __('resources/clients.validation.email_unique'),
    ]),
```

### 7. Page Titles

**Before:**
```php
class ViewClient extends ViewRecord
{
    protected static string $view = 'filament.app.resources.clients.pages.view-client';
    protected static ?string $title = 'View client';
    protected static ?string $navigationLabel = 'View client';
}
```

**After:**
```php
class ViewClient extends ViewRecord
{
    protected static string $view = 'filament.app.resources.clients.pages.view-client';
    protected static ?string $title = null;
    protected static ?string $navigationLabel = null;

    public function getTitle(): string
    {
        return __('resources/clients.pages.view');
    }

    public static function getNavigationLabel(): string
    {
        return __('resources/clients.pages.view');
    }
}
```

### 8. Filters

**Before:**
```php
SelectFilter::make('payment_method')
    ->label('Payment method')
    ->options([...]),

Filter::make('created_at')
    ->form([
        DatePicker::make('from')->label('From'),
        DatePicker::make('to')->label('To'),
    ]),
```

**After:**
```php
SelectFilter::make('payment_method')
    ->label(__('resources/invoices.filters.payment_method'))
    ->options([...]),

Filter::make('created_at')
    ->form([
        DatePicker::make('from')->label(__('resources/reservations.filters.from')),
        DatePicker::make('to')->label(__('resources/reservations.filters.to')),
    ]),
```

### 9. Tooltips and Descriptions

**Before:**
```php
IconColumn::make('fiscalized')
    ->boolean()
    ->tooltip(fn ($state): string => $state
        ? 'Invoice successfully fiscalized'
        : 'Invoice not fiscalized'
    ),

FileUpload::make('attachments')
    ->helperText('Add PDFs or images as extra attachments.'),
```

**After:**
```php
IconColumn::make('fiscalized')
    ->boolean()
    ->tooltip(fn ($state): string => $state
        ? __('resources/invoices.tooltips.fiscalized')
        : __('resources/invoices.tooltips.not_fiscalized')
    ),

FileUpload::make('attachments')
    ->helperText(__('resources/email.send_email.descriptions.additional_attachments')),
```

### 10. Widget Stats

**Before:**
```php
Stat::make('previous_visit', $previousVisit)
    ->description('Previous visit')
    ->icon('heroicon-o-calendar'),
```

**After:**
```php
Stat::make('previous_visit', $previousVisit)
    ->description(__('resources/clients.stats.previous_visit'))
    ->icon('heroicon-o-calendar'),
```

### 11. Placeholders

**Before:**
```php
Textarea::make('remarks')
    ->label('Remarks')
    ->placeholder('Enter remarks or notes about the patient...'),
```

**After:**
```php
Textarea::make('remarks')
    ->label(__('resources/patients.fields.remarks'))
    ->placeholder(__('resources/patients.placeholders.remarks')),
```

### 12. Repeater Items

**Before:**
```php
Repeater::make('items')
    ->label('Invoice items')
    ->addActionLabel('Add item')
    ->schema([...]),
```

**After:**
```php
Repeater::make('items')
    ->label(__('resources/invoices.fields.invoice_items'))
    ->addActionLabel(__('resources/invoices.items.add_item'))
    ->schema([...]),
```

## Best Practices

### 1. **Use Common Translations for Shared Fields**
Fields like "Email", "Phone", "Name" should use `common.php`:
```php
->label(__('common.fields.email'))  // Good
->label(__('resources/clients.fields.email'))  // Avoid (unless specific context)
```

### 2. **Use Resource-Specific Translations for Context**
When a field has specific meaning in a resource:
```php
// In invoices, "Total" refers to invoice total
->label(__('resources/invoices.fields.total'))

// In general context
->label(__('common.fields.total'))
```

### 3. **Organize Translations by Feature**
Group related translations together:
```php
'send_email' => [
    'title' => 'Send email',
    'fields' => [...],
    'validation' => [...],
    'messages' => [...],
]
```

### 4. **Use Dot Notation Consistently**
Always use the slash (/) for directory separator and dot (.) for array keys:
```php
__('resources/clients.fields.full_name')  // ✓ Good
__('resources.clients.fields.full_name')  // ✗ Wrong
```

### 5. **Provide Both Arabic and English**
Always create translations in both languages:
```php
// lang/en/resources/clients.php
'fields' => ['name' => 'Name'],

// lang/ar/resources/clients.php
'fields' => ['name' => 'الاسم'],
```

## Translation Keys Reference

### Common Fields (common.php)
- `common.fields.email`
- `common.fields.phone`
- `common.fields.address`
- `common.fields.name`
- `common.fields.created_at`
- `common.fields.updated_at`

### Common Actions (common.php)
- `common.actions.save`
- `common.actions.cancel`
- `common.actions.delete`
- `common.actions.edit`
- `common.actions.view`

### Common Messages (common.php)
- `common.messages.success`
- `common.messages.error`
- `common.messages.saved`
- `common.messages.deleted`

### Navigation (navigation.php)
- `navigation.dashboard`
- `navigation.clients`
- `navigation.patients`
- `navigation.groups.finance`

### Resource Structure Pattern
```
resources/{resource}.php
├── navigation          # Navigation label
├── singular            # Singular form
├── plural              # Plural form
├── fields              # Field labels
├── tabs                # Tab labels
├── pages               # Page titles
├── actions             # Action labels
├── filters             # Filter labels
├── validation          # Validation messages
└── tooltips            # Tooltip texts
```

## Testing Translations

### Switch Language in UI
Users can switch between English and Arabic using the language switcher in the user menu (top-right corner).

### Test RTL Support (Arabic)
When Arabic is selected:
- Layout should flip to right-to-left
- Text alignment should be right-aligned
- Icons and controls should mirror positions

## Need More Translations?

To add more resource translations:

1. Create new translation files:
```bash
lang/en/resources/your_resource.php
lang/ar/resources/your_resource.php
```

2. Follow the structure of existing resource files

3. Use the translations in your Resource class with `__()`

## Summary

- **Always use `__()`** for user-facing text
- **Use `common.php`** for shared fields/actions
- **Use `resources/{name}.php`** for resource-specific content
- **Provide both English and Arabic** translations
- **Test with language switcher** in the dashboard

Happy translating! 🌍
