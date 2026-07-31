String estadoBoletoLabel(String estado) {
  switch (estado) {
    case 'valido':    return 'Válido';
    case 'usado':     return 'Usado';
    case 'cancelado': return 'Cancelado';
    case 'vencido':   return 'Vencido';
    default:          return estado;
  }
}
