<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Cita Agendada</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #007bff; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f8f9fa; }
        .footer { text-align: center; padding: 20px; color: #666; }
        .cita-details { background: white; padding: 15px; border-radius: 5px; margin: 15px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔧 Taller de Vehículos</h1>
            <h2>Cita Agendada Exitosamente</h2>
        </div>
        
        <div class="content">
            <p>Estimado/a <strong>{{ $cita->usuario->nombre }}</strong>,</p>
            
            <p>Su cita ha sido agendada exitosamente. A continuación los detalles:</p>
            
            <div class="cita-details">
                <h3>Detalles de la Cita</h3>
                <p><strong>Fecha:</strong> {{ $cita->fecha->format('d/m/Y') }}</p>
                <p><strong>Hora:</strong> {{ $cita->hora->format('H:i') }}</p>
                <p><strong>Tipo de Servicio:</strong> {{ $cita->tipo_servicio ?? 'No especificado' }}</p>
                <p><strong>Descripción:</strong> {{ $cita->descripcion }}</p>
                
                @if($cita->vehiculo_marca)
                <h4>Información del Vehículo</h4>
                <p><strong>Marca:</strong> {{ $cita->vehiculo_marca }}</p>
                <p><strong>Modelo:</strong> {{ $cita->vehiculo_modelo }}</p>
                <p><strong>Placa:</strong> {{ $cita->vehiculo_placa }}</p>
                @endif
                
                <p><strong>Estado:</strong> <span style="color: #28a745;">{{ ucfirst($cita->estado) }}</span></p>
            </div>
            
            <p>Por favor, llegue 10 minutos antes de su cita programada.</p>
            
            <p>Si necesita cancelar o reprogramar su cita, puede hacerlo a través de nuestro sistema o contactándonos directamente.</p>
        </div>
        
        <div class="footer">
            <p>Gracias por confiar en nosotros</p>
            <p><strong>Taller de Vehículos</strong></p>
        </div>
    </div>
</body>
</html>
