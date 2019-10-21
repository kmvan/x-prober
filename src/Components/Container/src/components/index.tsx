import React, { HTMLAttributes } from 'react'
import styled from 'styled-components'
import { device } from '~components/Style/src/components/devices'
import { GUTTER } from '~components/Config/src'

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
interface IContainer extends HTMLAttributes<HTMLDivElement> {}

const Container = (props: IContainer) => {
  return <StyledContainer {...props} />
}

export default Container
