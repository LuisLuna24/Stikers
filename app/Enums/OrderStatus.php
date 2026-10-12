<?php

namespace App\Enums;

enum OrderStatus: string
{
    case PendingDeposit = 'pending_deposit';
    case InReview = 'in_review';
    case InProduction = 'in_production';
    case ReadyForPickup = 'ready_for_pickup';
    case DeliveredBalanceDue = 'delivered_balance_due';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
    case Refunded = 'refunded';

    public function label(): string
    {
        return match ($this) {
            self::PendingDeposit => 'Anticipo pendiente',
            self::InReview => 'En revisión',
            self::InProduction => 'En producción',
            self::ReadyForPickup => 'Lista para entrega',
            self::DeliveredBalanceDue => 'Entregada · saldo pendiente',
            self::Completed => 'Completada',
            self::Cancelled => 'Cancelada',
            self::Refunded => 'Reembolsada',
        };
    }

    public function classes(): string
    {
        return match ($this) {
            self::Completed, self::ReadyForPickup => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-400/10 dark:text-emerald-300',
            self::InProduction => 'bg-blue-50 text-blue-700 dark:bg-blue-400/10 dark:text-blue-300',
            self::Cancelled, self::Refunded => 'bg-rose-50 text-rose-700 dark:bg-rose-400/10 dark:text-rose-300',
            default => 'bg-amber-50 text-amber-700 dark:bg-amber-400/10 dark:text-amber-300',
        };
    }

    public function dotClasses(): string
    {
        return match ($this) {
            self::Completed, self::ReadyForPickup => 'bg-emerald-500',
            self::InProduction => 'bg-blue-500',
            self::Cancelled, self::Refunded => 'bg-rose-500',
            default => 'bg-amber-500',
        };
    }

    public function next(): ?self
    {
        return match ($this) {
            self::PendingDeposit => self::InReview,
            self::InReview => self::InProduction,
            self::InProduction => self::ReadyForPickup,
            self::ReadyForPickup => self::DeliveredBalanceDue,
            self::DeliveredBalanceDue => self::Completed,
            self::Completed, self::Cancelled, self::Refunded => null,
        };
    }

    public function actionLabel(): ?string
    {
        return match ($this) {
            self::PendingDeposit => 'Pasar a revisión',
            self::InReview => 'Mandar a producción',
            self::InProduction => 'Marcar lista para entrega',
            self::ReadyForPickup => 'Confirmar entrega',
            self::DeliveredBalanceDue => 'Completar pedido',
            self::Completed => 'Reembolsar pedido',
            self::Cancelled, self::Refunded => null,
        };
    }

    public function canCancel(): bool
    {
        return in_array($this, [self::PendingDeposit, self::InReview, self::InProduction, self::ReadyForPickup], true);
    }

    public static function tryFromValue(?string $value): ?self
    {
        return $value ? self::tryFrom($value) : null;
    }
}
