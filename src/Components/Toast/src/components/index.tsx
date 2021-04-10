import { GUTTER } from '@/Config/src'
import { gettext } from '@/Language/src'
import { Portal } from '@/Utils/src/components/portal'
import { observer } from 'mobx-react-lite'
import React from 'react'
import styled from 'styled-components'
import { ToastStore } from '../stores'
const StyledToast = styled.div`
  position: fixed;
  bottom: 4rem;
  width: 20rem;
  max-width: 80vw;
  left: 50%;
  transform: translateX(-50%);
  background: ${({ theme }) => theme['toast.bg']};
  color: ${({ theme }) => theme['toast.fg']};
  border-radius: ${GUTTER};
  padding: calc(${GUTTER} / 2) ${GUTTER};
  cursor: pointer;
  word-break: normal;
  text-align: center;
  backdrop-filter: blur(5px);
`
export const Toast = observer(() => {
  const { isOpen, msg, close } = ToastStore
  if (!isOpen) {
    return null
  }
  return (
    <Portal>
      <StyledToast title={gettext('Click to close')} onClick={() => close()}>
        {msg}
      </StyledToast>
    </Portal>
  )
})
