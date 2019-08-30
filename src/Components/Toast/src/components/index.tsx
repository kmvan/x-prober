import React, { Component } from 'react'
import { observer } from 'mobx-react'
import Portal from '~components/Helper/src/components/portal'
import store from '../stores'
import styled from 'styled-components'
import { GUTTER } from '~components/Config/src'

const ToastContainer = styled.div`
  position: fixed;
  bottom: 4rem;
  width: 20vw;
  max-width: 80vw;
  left: 50%;
  transform: translateX(-50%);
  background: rgba(0, 0, 0, 0.85);
  color: #fff;
  border-radius: ${GUTTER};
  padding: ${GUTTER};
`

@observer
class Toast extends Component {
  public render() {
    const { isOpen, msg } = store

    if (!isOpen) {
      return null
    }

    return (
      <Portal>
        <ToastContainer>{msg}</ToastContainer>
      </Portal>
    )
  }
}

export default Toast
