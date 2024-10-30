export const useChat = () => {
  const resolveAvatarBadgeVariant = status => {
    if (status === 'online' || status == 1)
      return 'success'
    if (status === 'busy')
      return 'error'
    if (status === 'away')
      return 'warning'
    
    return 'warning'
  }

  return {
    resolveAvatarBadgeVariant,
  }
}
