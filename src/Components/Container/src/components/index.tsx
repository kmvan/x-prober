import React, { FC } from 'react'
import styled from 'styled-components'
import { GUTTER } from '../../../Config/src'
import { device } from '../../../Style/src/components/devices'

const StyledContainer = styled.div`
  margin-left: auto;
  margin-right: auto;
  padding-left: calc(${GUTTER} / 2);
  padding-right: calc(${GUTTER} / 2);
  @media ${device('desktopSm')} {
    padding-left: ${GUTTER};
    padding-right: ${GUTTER};
  }
`
export const Container: FC = (props) => <StyledContainer {...props} />
