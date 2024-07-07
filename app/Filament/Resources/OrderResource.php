<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use App\Models\Product;
use Faker\Core\Number;
use Filament\Actions\ActionGroup;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()->schema([
                    Forms\Components\Section::make("Order Information")->schema([
                        Forms\Components\Select::make("user_id")
                        ->label("Customer")
                        ->relationship("user" , "name")
                        ->searchable()
                        ->preload()
                        ->required(),
                        Forms\Components\Select::make("payment_method")->options([
                            "stripe"=>"Stripe",
                            "cod" => "Cash On Delivery",

                            ])->required(),
                        Forms\Components\Select::make("payment_status")->options([
                            "pending" => "Pending",
                            "paid" => "Paid",
                            "failed" => "Failed",

                        ])->default("pending"),

                        Forms\Components\ToggleButtons::make("status")->options([
                            "new" => "New",
                            "processing" => "Processing",
                            "shipped" => "Shipped",
                            "delivered" => "Delivered",
                            "cancelled" => "Cancelled"
                        ])->inline()->default("new")->required()->colors([
                            "new" => "info",
                            "processing" => "warning",
                            "shipped" => "success",
                            "delivered" => "success",
                            "cancelled" => "danger"
                        ])->icons([
                            "new" => "heroicon-m-sparkles",
                            "processing" => "heroicon-m-arrow-path",
                            "shipped" => "heroicon-m-truck",
                            "delivered" => "heroicon-m-check-badge",
                            "cancelled" => "heroicon-m-x-circle"
                        ]),
                        Forms\Components\Select::make("currency")->options([
                            "ir" => "RIAL",
                            "usd" => "USD",
                            "eur" => "EUR",
                            "gbp" => "GBP"
                        ])->default("ir"),

                        Forms\Components\Select::make("shipping_method")->options([
                            "fedex" => "FedEx",
                            "ups" => "UPS",
                            "dhl" => "DHL"
                        ])->required()->default("fedex"),

                        Forms\Components\Textarea::make("notes")->columnSpanFull()
                        ])->columns(2),
                    Forms\Components\Section::make("Order Items")->schema([
                        Forms\Components\Repeater::make("items")->relationship()->schema([
                            Forms\Components\Select::make("product_id")->relationship("product" ,"name")
                                ->searchable()
                                ->preload()
                                ->required()
                                ->distinct()
                                ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                ->columnSpan(4)
                                ->reactive()
                                ->afterStateUpdated(fn($state , Forms\Set $set )=>$set("unit_amount" , Product::find($state)?->price ?? 0 ))
                                ->afterStateUpdated(fn($state , Forms\Set $set )=>$set("total_amount" , Product::find($state)?->price ?? 0 )),

                            Forms\Components\TextInput::make("quantity")
                            ->numeric()
                            ->required()
                            ->default(1)
                            ->minValue(1)
                            ->columnSpan(2)
                            ->reactive()
                            ->afterStateUpdated(fn($state , Forms\Set $set , Forms\Get $get)=>$set("total_amount" , $state*$get("unit_amount"))),

                            Forms\Components\TextInput::make("unit_amount")
                            ->numeric()
                            ->required()
                            ->disabled()
                            ->dehydrated()
                            ->columnSpan(3),

                            Forms\Components\TextInput::make("total_amount")
                            ->numeric()
                            ->required()
                            ->dehydrated()
                            ->columnSpan(3)
                        ])->columns(12),

                        Forms\Components\Placeholder::make("grand_total_placeholder")->label("Grand Total")
                        ->content(function(Forms\Get $get , Forms\Set $set){
                            $total = 0;
                            if(!$repeaters = $get("items")){
                                return $total;
                            }
                            foreach ($repeaters as $key=>$repeater){
                                $total +=$get("items.{$key}.total_amount");
                            }
                            $set("grand_total" , $total);
                            return \Illuminate\Support\Number::currency($total , "USD");
                        }),

                        Forms\Components\Hidden::make("grand_total")->default(0)
                    ]),
                    ])->columnSpanFull()
                ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make("user.name")->sortable()->searchable(),
                Tables\Columns\TextColumn::make("grand_total")->numeric()->sortable()->money("USD"),
                Tables\Columns\TextColumn::make("payment_method")->searchable()->sortable(),
                Tables\Columns\TextColumn::make("payment_status")->searchable()->sortable(),
                Tables\Columns\TextColumn::make("currency")->sortable()->searchable(),
                Tables\Columns\TextColumn::make("shipping_method")->sortable()->searchable(),
                Tables\Columns\SelectColumn::make("status")->options([
                    "new" => "New",
                    "processing" => "Processing",
                    "shipped" => "Shipped",
                    "delivered" => "Delivered",
                    "cancelled" => "Cancelled"
                ])->searchable()->sortable(),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }


    public static function getNavigationBadgeColor(): string|array|null
    {
        return static::getModel()::count() > 10 ?"success" : "danger";
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
