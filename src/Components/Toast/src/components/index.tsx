import React from 'react'
import Portal from '@/Helper/src/components/portal'
import store from '../stores'
import styled from 'styled-components'
import { GUTTER } from '@/Config/src'
import { gettext } from '@/Language/src'
import { rgba } from 'polished'
import { observer } from 'mobx-react-lite'

const StyledToast = styled.div`
  position: fixed;
  bottom: 4rem;
  width: 20rem;
  max-width: 80vw;
  left: 50%;
  transform: translateX(-50%);
  background: ${({ theme }) => rgba(theme.colorDark, 0.85)};
  color: ${({ theme }) => theme.colorGray};
  border-radius: ${GUTTER};
  padding: calc(${GUTTER} / 2) ${GUTTER};
  cursor: pointer;
  word-break: normal;
  text-align: center;
  backdrop-filter: blur(5px);
`

const Toast = observer(() => {
  const { isOpen, msg, close } = store

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

export default Toast
