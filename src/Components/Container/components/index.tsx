import { FC } from 'react'
import styled from 'styled-components'
import { GUTTER, MAX_WIDTH } from '../../Config'
import { device } from '../../Style/components/devices'
const StyledContainer = styled.div`
  margin-left: auto;
  margin-right: auto;
  padding-left: calc(${GUTTER} / 2);
  padding-right: calc(${GUTTER} / 2);
  max-width: ${MAX_WIDTH};
  @media ${device('desktopSm')} {
    padding-left: ${GUTTER};
    padding-right: ${GUTTER};
  }
`
export const Container: FC = (props) => <StyledContainer {...props} />
