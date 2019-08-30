import React from 'react'
import styled, { css } from 'styled-components'
import { breakPoints, device } from '~components/Style/src/components/devices'
import { GUTTER } from '~components/Config/src'

const Div = styled.div`
  ${Object.entries(breakPoints).map(([id, px]) => {
    return css`
      @media ${device(id)} {
        padding-left: ${GUTTER};
        padding-right: ${GUTTER};
        max-width: ${px};
        margin-left: auto;
        margin-right: auto;
      }
    `
  })}
`

const Container = props => {
  return <Div {...props} />
}

export default Container
